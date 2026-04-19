import React, { useState } from 'react';
import { Container, Row, Col, Card, Button, Badge, Modal, Form } from 'react-bootstrap';
import { BookOpen, Star, MessageSquare, PlusCircle, BarChart2 } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import { useAppContext } from '../context/AppContext';
import BookCard from '../components/BookCard';

const AuthorDashboard = () => {
  const { user, reviews, books, addBook } = useAppContext();
  const navigate = useNavigate();
  
  // Local state for UI
  const [showAddModal, setShowAddModal] = useState(false);
  const [showReviewsModal, setShowReviewsModal] = useState(false);
  const [selectedBookObj, setSelectedBookObj] = useState(null);
  const [newBook, setNewBook] = useState({ title: '', category: 'Fiction', description: '', pages: '', cover: 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&q=80&w=400', content: '', pdfUrl: '' });

  // Filter books manually for the logged-in author
  const authorBooks = books.filter(b => b.author.toLowerCase() === (user?.name || '').toLowerCase());
  
  // Calculate mock stats
  const totalBooks = authorBooks.length;
  const avgRating = totalBooks > 0 ? (authorBooks.reduce((acc, curr) => acc + curr.rating, 0) / totalBooks).toFixed(1) : 0;
  const totalReaders = totalBooks * 1240; // mock number

  const handleAddBook = (e) => {
    e.preventDefault();
    const newId = books.length > 0 ? Math.max(...books.map(b => b.id)) + 1 : 1;
    const bookEntry = {
      ...newBook,
      id: newId,
      author: user?.name || 'Unknown Author',
      rating: 0,
      published: new Date().getFullYear(),
      isbn: `978-${Math.floor(Math.random() * 1000000000)}`,
      pages: parseInt(newBook.pages) || 200,
    };
    addBook(bookEntry);
    setShowAddModal(false);
    setNewBook({ title: '', category: 'Fiction', description: '', pages: '', cover: 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&q=80&w=400', content: '', pdfUrl: '' });
  };

  const openReviews = (book) => {
    setSelectedBookObj(book);
    setShowReviewsModal(true);
  };

  if (!user || user.role !== 'author') {
    return (
      <Container className="py-5 text-center min-vh-100 d-flex flex-column justify-content-center align-items-center">
        <h2 className="fw-bold text-danger">Access Denied</h2>
        <p className="text-muted">You do not have permission to view the Author Space.</p>
        <Button variant="outline-purple" onClick={() => navigate('/')}>Return Home</Button>
      </Container>
    );
  }

  return (
    <div className="py-5 bg-light min-vh-100">
      <Container>
        {/* Header */}
        <div className="d-flex justify-content-between align-items-end mb-5">
          <div>
            <Badge bg="warning" text="dark" className="px-3 py-2 rounded-pill mb-3 fw-bold">Author Space</Badge>
            <h2 className="fw-bold mb-2">Hello, {user.name}</h2>
            <p className="text-muted mb-0">Manage your published works and track your success.</p>
          </div>
          <Button className="btn-purple d-flex align-items-center gap-2 px-4 py-2" onClick={() => setShowAddModal(true)}>
            <PlusCircle size={20} /> Add New Book
          </Button>
        </div>

        {/* Stats Section */}
        <Row className="g-4 mb-5">
          <Col md={4}>
            <Card className="border-0 shadow-sm rounded-4 h-100 hover-lift">
              <Card.Body className="p-4 d-flex align-items-center gap-3">
                <div className="bg-purple text-white p-3 rounded-circle d-flex align-items-center justify-content-center">
                  <BookOpen size={24} />
                </div>
                <div>
                  <h3 className="fw-bold mb-0 text-dark">{totalBooks}</h3>
                  <p className="text-muted mb-0 small">Published Books</p>
                </div>
              </Card.Body>
            </Card>
          </Col>
          <Col md={4}>
            <Card className="border-0 shadow-sm rounded-4 h-100 hover-lift">
              <Card.Body className="p-4 d-flex align-items-center gap-3">
                <div className="bg-success text-white p-3 rounded-circle d-flex align-items-center justify-content-center">
                  <BarChart2 size={24} />
                </div>
                <div>
                  <h3 className="fw-bold mb-0 text-dark">{totalReaders.toLocaleString()}</h3>
                  <p className="text-muted mb-0 small">Total Readers</p>
                </div>
              </Card.Body>
            </Card>
          </Col>
          <Col md={4}>
            <Card className="border-0 shadow-sm rounded-4 h-100 hover-lift">
              <Card.Body className="p-4 d-flex align-items-center gap-3">
                <div className="bg-warning text-white p-3 rounded-circle d-flex align-items-center justify-content-center">
                  <Star size={24} />
                </div>
                <div>
                  <h3 className="fw-bold mb-0 text-dark">{avgRating}</h3>
                  <p className="text-muted mb-0 small">Average Rating</p>
                </div>
              </Card.Body>
            </Card>
          </Col>
        </Row>

        {/* My Works */}
        <h3 className="fw-bold mb-4">Your Library</h3>
        {authorBooks.length > 0 ? (
          <Row className="g-4">
            {authorBooks.map(book => (
              <Col key={book.id} xs={12} sm={6} lg={3}>
                <div className="position-relative">
                  <BookCard book={book} />
                  <Button 
                    variant="light" 
                    className="position-absolute top-0 end-0 m-3 shadow-sm text-purple rounded-pill fw-bold d-flex align-items-center gap-1"
                    onClick={(e) => { e.stopPropagation(); openReviews(book); }}
                    style={{ zIndex: 10, fontSize: '0.8rem' }}
                  >
                    <MessageSquare size={14} /> Reviews
                  </Button>
                </div>
              </Col>
            ))}
          </Row>
        ) : (
          <div className="text-center py-5 bg-white rounded-4 shadow-sm border border-dashed">
            <BookOpen size={48} className="text-muted mb-3 opacity-50" />
            <h5 className="fw-bold text-muted">No Books Published Yet</h5>
            <p className="text-muted small mb-4">Start your journey as an author by adding your first book.</p>
            <Button variant="outline-purple" onClick={() => setShowAddModal(true)}>Add Your First Book</Button>
          </div>
        )}
      </Container>

      {/* Add Book Modal */}
      <Modal show={showAddModal} onHide={() => setShowAddModal(false)} centered size="lg">
        <Modal.Header closeButton className="border-0 pb-0">
          <Modal.Title className="fw-bold">Publish New Book</Modal.Title>
        </Modal.Header>
        <Modal.Body className="p-4">
          <Form onSubmit={handleAddBook}>
            <Row className="g-3">
              <Col md={8}>
                <Form.Group className="mb-3">
                  <Form.Label className="fw-medium">Book Title</Form.Label>
                  <Form.Control type="text" required value={newBook.title} onChange={(e) => setNewBook({...newBook, title: e.target.value})} />
                </Form.Group>
                <Form.Group className="mb-3">
                  <Form.Label className="fw-medium">Description</Form.Label>
                  <Form.Control as="textarea" rows={3} required value={newBook.description} onChange={(e) => setNewBook({...newBook, description: e.target.value})} />
                </Form.Group>
                <Form.Group className="mb-3">
                  <Form.Label className="fw-medium">Written Content (Optional)</Form.Label>
                  <Form.Control as="textarea" rows={3} value={newBook.content} onChange={(e) => setNewBook({...newBook, content: e.target.value})} placeholder="Paste your book's text here..." />
                </Form.Group>
                <Form.Group className="mb-3">
                  <Form.Label className="fw-medium">Attach PDF File</Form.Label>
                  <Form.Control type="file" accept="application/pdf" onChange={(e) => { 
                    const file = e.target.files[0]; 
                    if (file) { 
                      setNewBook({...newBook, pdfUrl: URL.createObjectURL(file)}); 
                    } 
                  }} />
                </Form.Group>
              </Col>
              <Col md={4}>
                <Form.Group className="mb-3">
                  <Form.Label className="fw-medium">Category</Form.Label>
                  <Form.Select value={newBook.category} onChange={(e) => setNewBook({...newBook, category: e.target.value})}>
                    <option value="Fiction">Fiction</option>
                    <option value="Science">Science</option>
                    <option value="History">History</option>
                    <option value="Technology">Technology</option>
                  </Form.Select>
                </Form.Group>
                <Form.Group className="mb-3">
                  <Form.Label className="fw-medium">Page Count</Form.Label>
                  <Form.Control type="number" required value={newBook.pages} onChange={(e) => setNewBook({...newBook, pages: e.target.value})} />
                </Form.Group>
                <Form.Group className="mb-3">
                  <Form.Label className="fw-medium">Cover Image URL</Form.Label>
                  <Form.Control type="url" required value={newBook.cover} onChange={(e) => setNewBook({...newBook, cover: e.target.value})} />
                </Form.Group>
              </Col>
            </Row>
            <div className="d-flex justify-content-end mt-4">
              <Button variant="light" className="me-2" onClick={() => setShowAddModal(false)}>Cancel</Button>
              <Button type="submit" className="btn-purple">Publish Book</Button>
            </div>
          </Form>
        </Modal.Body>
      </Modal>

      {/* Reviews Modal */}
      <Modal show={showReviewsModal} onHide={() => setShowReviewsModal(false)} centered>
        <Modal.Header closeButton className="border-0 pb-0 bg-light">
          <Modal.Title className="fw-bold d-flex align-items-center gap-2">
            <MessageSquare size={20} className="text-purple" /> Reader Reviews
          </Modal.Title>
        </Modal.Header>
        <Modal.Body className="p-0">
          {selectedBookObj && (
            <div className="p-4 bg-light border-bottom">
              <h5 className="fw-bold mb-1">{selectedBookObj.title}</h5>
              <div className="text-warning d-flex align-items-center gap-1 small fw-bold">
                <Star size={14} fill="currentColor" /> {selectedBookObj.rating > 0 ? selectedBookObj.rating.toFixed(1) : 'No Ratings'}
              </div>
            </div>
          )}
          <div className="p-4">
            {selectedBookObj && reviews[selectedBookObj.id] && reviews[selectedBookObj.id].length > 0 ? (
              <div className="d-flex flex-column gap-4">
                {reviews[selectedBookObj.id].map((rev, idx) => (
                  <div key={idx} className="border-bottom pb-3">
                    <div className="d-flex justify-content-between align-items-center mb-1">
                      <span className="fw-bold">{rev.author}</span>
                      <span className="text-muted small">{rev.date}</span>
                    </div>
                    <div className="text-warning mb-2 d-flex gap-1">
                      {[...Array(5)].map((_, i) => (
                        <Star key={i} size={12} fill={i < rev.rating ? "currentColor" : "none"} className={i < rev.rating ? "" : "text-muted opacity-25"} />
                      ))}
                    </div>
                    <p className="text-muted small mb-0">{rev.text}</p>
                  </div>
                ))}
              </div>
            ) : (
              <div className="text-center py-4">
                <MessageSquare size={32} className="text-muted opacity-25 mb-3" />
                <p className="text-muted">No reviews available for this book yet.</p>
              </div>
            )}
          </div>
        </Modal.Body>
      </Modal>
    </div>
  );
};

export default AuthorDashboard;
