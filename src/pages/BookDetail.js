import React, { useState } from 'react';
import { Container, Row, Col, Button, Badge, Form } from 'react-bootstrap';
import { ArrowLeft, Star, FileText, Calendar, Hash, BookOpen, Heart, MessageSquare, Download, Crown, Lock } from 'lucide-react';
import { useParams, useNavigate } from 'react-router-dom';
import BookCard from '../components/BookCard';
import { useAppContext } from '../context/AppContext';

const BookDetail = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const { user, favorites, toggleFavorite, reviews, addReview, books } = useAppContext();
  
  const [newReviewText, setNewReviewText] = useState('');
  const [newReviewRating, setNewReviewRating] = useState(5);
  
  const book = books.find(b => b.id === parseInt(id)) || books[0];
  const similarBooks = books.filter(b => b.id !== book.id).slice(0, 3);
  const isFavorite = favorites.includes(book.id);
  const bookReviews = reviews[book.id] || [];

  const hasAccess = !book.isPremium || (user && (user.isPremium || user.role === 'admin'));

  const handleDownloadPDF = () => {
    const content = `SmartLibrary Premium Book Download\n\nTitle: ${book.title}\nAuthor: ${book.author}\n\nThis is a mock PDF generated securely for Premium Members.\nEnjoy your read!`;
    const blob = new Blob([content], { type: 'application/pdf' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `${book.title.replace(/\s+/g, '_')}_Premium.pdf`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
  };

  const handleReviewSubmit = (e) => {
    e.preventDefault();
    if (!newReviewText.trim()) return;
    
    addReview(book.id, {
      author: user.name,
      rating: newReviewRating,
      text: newReviewText,
      date: new Date().toLocaleDateString()
    });
    setNewReviewText('');
    setNewReviewRating(5);
  };

  return (
    <div className="py-4 bg-light min-vh-100">
      <Container>
        <Button 
          variant="link" 
          className="text-dark text-decoration-none d-flex align-items-center gap-2 mb-4 px-0 fw-medium"
          onClick={() => navigate('/books')}
        >
          <ArrowLeft size={18} /> Back to Catalog
        </Button>

        <Row className="g-4 mb-5">
          {/* Left Column - Book Cover */}
          <Col lg={4} md={5}>
            <div className="rounded-4 overflow-hidden shadow-sm bg-white p-2">
              <img 
                src={book.cover} 
                alt={book.title} 
                className="w-100 rounded-3"
                style={{ objectFit: 'cover', minHeight: '400px' }}
              />
            </div>
          </Col>

          {/* Right Column - Book Info */}
          <Col lg={8} md={7}>
            <div className="bg-white p-4 p-lg-5 rounded-4 shadow-sm">
              <Badge bg="purple" className="mb-3 rounded-pill px-3 py-2" style={{ backgroundColor: 'var(--primary-purple)' }}>
                {book.category}
              </Badge>
              
              <h1 className="fw-bold mb-2">{book.title}</h1>
              <p className="fs-5 text-muted mb-4">by {book.author}</p>

              <div className="d-flex align-items-center gap-2 text-warning fw-bold mb-4">
                <div className="d-flex">
                  {[...Array(5)].map((_, i) => (
                    <Star key={i} size={20} fill={i < Math.floor(book.rating) ? "currentColor" : "none"} />
                  ))}
                </div>
                <span className="text-dark ms-2">{book.rating} / 5.0</span>
              </div>

              <Row className="g-3 mb-4 pb-4 border-bottom text-muted">
                <Col sm={4} className="d-flex align-items-center gap-2">
                  <FileText size={18} /> {book.pages} pages
                </Col>
                <Col sm={4} className="d-flex align-items-center gap-2">
                  <Calendar size={18} /> Published {book.published}
                </Col>
                <Col sm={4} className="d-flex align-items-center gap-2">
                  <Hash size={18} /> {book.isbn}
                </Col>
              </Row>

              <div className="mb-5">
                <h4 className="fw-bold mb-3 d-flex align-items-center">
                  Description 
                  {book.isPremium && <Badge bg="warning" text="dark" className="ms-3 fs-6 rounded-pill"><Crown size={16} className="me-1"/> Premium Book</Badge>}
                </h4>
                <p className="text-muted lh-lg" style={{ wordBreak: 'break-word', overflowWrap: 'break-word' }}>
                  {hasAccess ? book.description : book.description.substring(0, 100) + '...'}
                </p>
              </div>

              {!hasAccess ? (
                <div className="bg-warning bg-opacity-10 p-4 rounded-4 border border-warning mb-4 text-center">
                  <Lock size={32} className="text-warning mb-2" />
                  <h5 className="fw-bold text-dark">Premium Content</h5>
                  <p className="text-muted mb-4">You need an active Premium Membership to read this book.</p>
                  <Button variant="warning" className="w-100 fw-bold py-3 text-dark d-flex justify-content-center align-items-center gap-2" onClick={() => navigate('/dashboard')}>
                    <Crown size={20} /> Upgrade to Premium Now
                  </Button>
                </div>
              ) : (
                <div className="d-flex flex-column gap-3">
                  <div className="d-flex gap-3">
                    <Button 
                      className="btn-purple flex-grow-1 d-flex justify-content-center align-items-center gap-2 py-3 fs-5" 
                      onClick={() => navigate(`/read/${book.id}`)}
                    >
                      <BookOpen size={20} /> Start Reading
                    </Button>
                    <Button 
                      variant="outline-secondary" 
                      className={`px-4 py-3 d-flex align-items-center justify-content-center rounded-3 ${isFavorite ? 'bg-light' : 'bg-white'}`}
                      style={{ borderColor: isFavorite ? 'var(--primary-purple)' : '#dee2e6' }} 
                      onClick={() => {
                        if (!user) {
                          navigate('/login');
                          return;
                        }
                        toggleFavorite(book.id);
                      }}
                    >
                      <Heart size={20} className="text-purple" style={{ color: 'var(--primary-purple)' }} fill={isFavorite ? "currentColor" : "none"} />
                    </Button>
                  </div>
                  
                  <Button 
                    variant={user && (user.isPremium || user.role === 'admin') ? "outline-success" : "outline-secondary"} 
                    className="w-100 py-3 d-flex justify-content-center align-items-center gap-2" 
                    onClick={() => {
                      if (user && (user.isPremium || user.role === 'admin')) {
                        handleDownloadPDF();
                      } else {
                        alert("Please upgrade to a Premium Subscription to download books.");
                        navigate('/dashboard');
                      }
                    }}
                  >
                    {user && (user.isPremium || user.role === 'admin') ? <Download size={20} /> : <Lock size={20} className="text-secondary" />}
                    {user && (user.isPremium || user.role === 'admin') ? "Download PDF (Premium Feature)" : "Download PDF (Premium Only)"}
                  </Button>
                </div>
              )}
            </div>
            
            {/* Reviews Section */}
            <div className="bg-white p-4 p-lg-5 rounded-4 shadow-sm mt-4">
              <h4 className="fw-bold mb-4 d-flex align-items-center gap-2">
                <MessageSquare size={20} className="text-purple" /> reader reviews 
                <Badge bg="light" text="dark" className="ms-2 border">{bookReviews.length}</Badge>
              </h4>
              
              {/* Review Submit Form */}
              {user ? (
                <div className="bg-light p-4 rounded-4 mb-5 border-start border-4 border-purple shadow-sm">
                  <h6 className="fw-bold mb-3 d-flex align-items-center gap-2">Leave your review, {user.name}</h6>
                  <Form onSubmit={handleReviewSubmit}>
                    <div className="mb-3 d-flex align-items-center gap-2">
                      <span className="text-muted small fw-medium">Your Rating:</span>
                      <div className="d-flex text-warning" style={{cursor: 'pointer'}}>
                        {[1, 2, 3, 4, 5].map((star) => (
                          <Star key={star} size={20} fill={star <= newReviewRating ? "currentColor" : "none"} className={star <= newReviewRating ? "" : "text-muted"} onClick={() => setNewReviewRating(star)} />
                        ))}
                      </div>
                    </div>
                    <Form.Group className="mb-3">
                      <Form.Control as="textarea" rows={3} placeholder="What did you think of this book?" value={newReviewText} onChange={(e) => setNewReviewText(e.target.value)} required />
                    </Form.Group>
                    <div className="d-flex justify-content-end">
                      <Button type="submit" className="btn-purple px-4 py-2 shadow-sm rounded-pill">Post Review</Button>
                    </div>
                  </Form>
                </div>
              ) : (
                <div className="bg-light p-4 rounded-4 mb-5 text-center border-dashed">
                  <p className="text-muted mb-3">Join our community to leave a review.</p>
                  <Button variant="outline-purple" onClick={() => navigate('/login')}>Sign in to review</Button>
                </div>
              )}

              {/* Reviews List */}
              {bookReviews.length > 0 ? (
                <div className="d-flex flex-column gap-4">
                  {bookReviews.map((rev, idx) => (
                    <div key={idx} className="border-bottom pb-4">
                      <div className="d-flex justify-content-between align-items-center mb-2">
                        <span className="fw-bold text-dark">{rev.author}</span>
                        <span className="text-muted small">{rev.date}</span>
                      </div>
                      <div className="text-warning mb-2 d-flex gap-1">
                        {[...Array(5)].map((_, i) => (
                          <Star key={i} size={14} fill={i < rev.rating ? "currentColor" : "none"} className={i < rev.rating ? "" : "text-muted opacity-25"} />
                        ))}
                      </div>
                      <p className="text-muted mb-0 lh-lg">{rev.text}</p>
                    </div>
                  ))}
                </div>
              ) : (
                <div className="text-center py-5">
                  <MessageSquare size={48} className="text-muted opacity-25 mb-3" />
                  <p className="text-muted mb-0">No reviews yet. Be the first to review this book!</p>
                </div>
              )}
            </div>
          </Col>
        </Row>

        {/* Similar Books */}
        {similarBooks.length > 0 && (
          <div className="mt-5">
            <h3 className="fw-bold mb-4">Similar Books</h3>
            <Row className="g-4">
              {similarBooks.map(b => (
                <Col key={b.id} xs={12} sm={6} md={4} lg={3}>
                  <BookCard book={b} />
                </Col>
              ))}
            </Row>
          </div>
        )}
      </Container>
    </div>
  );
};

export default BookDetail;
