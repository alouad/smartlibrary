import React from 'react';
import { Container, Row, Col, Card, ProgressBar, Button } from 'react-bootstrap';
import { BookOpen, Heart, Clock, Award, Crown, CheckCircle } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import BookCard from '../components/BookCard';
import { useAppContext } from '../context/AppContext';

const Dashboard = () => {
  const { user, favorites, becomeAuthor, upgradeToPremium, books } = useAppContext();
  const navigate = useNavigate();
  
  const readingBooks = books.slice(0, 2);
  const favoriteBooks = books.filter(b => favorites.includes(b.id));

  return (
    <div className="py-5 bg-light min-vh-100">
      <Container>
        {/* Welcome Header */}
        <div className="mb-5">
          <h2 className="fw-bold mb-2">Welcome Back, {user?.name || "Reader"}!</h2>
          <p className="text-muted">Here's what's happening in your digital library today.</p>
        </div>

        {user?.role !== 'author' && (
          <div className="bg-purple-gradient border border-purple p-4 rounded-4 shadow-sm mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
              <h4 className="fw-bold mb-1 text-white">Have a story to share?</h4>
              <p className="text-white text-opacity-75 mb-0">Join our community of creators. Publish your own books and track your success.</p>
            </div>
            <Button onClick={becomeAuthor} className="btn-light px-4 py-2 text-nowrap shadow-sm hover-lift text-purple fw-bold">
              Become an Author
            </Button>
          </div>
        )}

        {(!user?.isPremium && user?.role !== 'admin') && (
          <div className="bg-warning bg-opacity-10 border border-warning p-4 rounded-4 shadow-sm mb-5 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
              <h4 className="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                <Crown size={24} className="text-warning" /> Upgrade to Premium
              </h4>
              <p className="text-muted mb-0">Unlock exclusive premium books, PDF downloads, and support your favorite authors.</p>
            </div>
            <Button onClick={upgradeToPremium} variant="warning" className="px-4 py-3 text-nowrap shadow-sm hover-lift fw-bold">
              Upgrade Now
            </Button>
          </div>
        )}

        {user?.isPremium && (
          <div className="bg-success bg-opacity-10 border border-success p-4 rounded-4 shadow-sm mb-5 d-flex align-items-center gap-3">
            <CheckCircle size={32} className="text-success" />
            <div>
              <h5 className="fw-bold mb-1 text-success">Premium Membership Active</h5>
              <p className="text-muted mb-0 small">You have full access to all premium books, downloads, and features.</p>
            </div>
          </div>
        )}

        {/* Dashboard Stats */}
        <Row className="g-4 mb-5">
          <Col md={3} sm={6}>
            <Card className="border-0 shadow-sm rounded-4 h-100 hover-lift transition-all">
              <Card.Body className="p-4 d-flex align-items-center gap-3">
                <div className="bg-purple text-white p-3 rounded-circle d-flex align-items-center justify-content-center">
                  <BookOpen size={24} />
                </div>
                <div>
                  <h3 className="fw-bold mb-0 text-dark">12</h3>
                  <p className="text-muted mb-0 small">Books Read</p>
                </div>
              </Card.Body>
            </Card>
          </Col>
          <Col md={3} sm={6}>
            <Card className="border-0 shadow-sm rounded-4 h-100 hover-lift transition-all">
              <Card.Body className="p-4 d-flex align-items-center gap-3">
                <div className="bg-warning text-white p-3 rounded-circle d-flex align-items-center justify-content-center">
                  <Clock size={24} />
                </div>
                <div>
                  <h3 className="fw-bold mb-0 text-dark">45h</h3>
                  <p className="text-muted mb-0 small">Reading Time</p>
                </div>
              </Card.Body>
            </Card>
          </Col>
          <Col md={3} sm={6}>
            <Card className="border-0 shadow-sm rounded-4 h-100 hover-lift transition-all">
              <Card.Body className="p-4 d-flex align-items-center gap-3">
                <div className="bg-danger text-white p-3 rounded-circle d-flex align-items-center justify-content-center">
                  <Heart size={24} />
                </div>
                <div>
                  <h3 className="fw-bold mb-0 text-dark">{favorites.length}</h3>
                  <p className="text-muted mb-0 small">Favorites</p>
                </div>
              </Card.Body>
            </Card>
          </Col>
          <Col md={3} sm={6}>
            <Card className="border-0 shadow-sm rounded-4 h-100 hover-lift transition-all">
              <Card.Body className="p-4 d-flex align-items-center gap-3">
                <div className="bg-success text-white p-3 rounded-circle d-flex align-items-center justify-content-center">
                  <Award size={24} />
                </div>
                <div>
                  <h3 className="fw-bold mb-0 text-dark">Lvl {user?.level || 1}</h3>
                  <p className="text-muted mb-0 small">Bookworm</p>
                </div>
              </Card.Body>
            </Card>
          </Col>
        </Row>

        {/* Currently Reading */}
        <div className="mb-5">
          <div className="d-flex justify-content-between align-items-end mb-4">
            <h3 className="fw-bold mb-0">Currently Reading</h3>
          </div>
          <Row className="g-4">
            {readingBooks.map((book, idx) => (
              <Col lg={6} key={book.id}>
                <Card className="border-0 shadow-sm rounded-4 overflow-hidden h-100">
                  <Row className="g-0 h-100">
                    <Col sm={4}>
                      <img src={book.cover} alt={book.title} className="w-100 h-100" style={{ objectFit: 'cover', minHeight: '200px' }} />
                    </Col>
                    <Col sm={8}>
                      <Card.Body className="p-4 d-flex flex-column h-100">
                        <span className="text-purple fw-bold small mb-2">{book.category}</span>
                        <h5 className="fw-bold mb-1">{book.title}</h5>
                        <p className="text-muted small mb-4">{book.author}</p>
                        
                        <div className="mt-auto">
                          <div className="d-flex justify-content-between small text-muted mb-2">
                            <span>Progress</span>
                            <span className="fw-bold text-dark">{40 + (idx * 25)}%</span>
                          </div>
                          <ProgressBar variant="purple" now={40 + (idx * 25)} className="mb-3" style={{ height: '8px' }} />
                          <Button 
                            variant="outline-purple" 
                            className="w-100 py-2"
                            onClick={() => navigate(`/read/${book.id}`)}
                          >
                            Continue Reading
                          </Button>
                        </div>
                      </Card.Body>
                    </Col>
                  </Row>
                </Card>
              </Col>
            ))}
          </Row>
        </div>

        {/* My Favorites */}
        <div>
          <div className="d-flex justify-content-between align-items-end mb-4">
            <h3 className="fw-bold mb-0">My Favorites</h3>
          </div>
          {favoriteBooks.length > 0 ? (
            <Row className="g-4">
              {favoriteBooks.map(book => (
                <Col key={book.id} xs={12} sm={6} lg={3}>
                  <BookCard book={book} />
                </Col>
              ))}
            </Row>
          ) : (
            <p className="text-muted">You haven't added any books to your favorites yet.</p>
          )}
        </div>

      </Container>
    </div>
  );
};

export default Dashboard;
