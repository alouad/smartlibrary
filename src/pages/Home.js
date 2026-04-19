import React, { useState } from 'react';
import { Container, Row, Col, Form, Button, Badge } from 'react-bootstrap';
import { Search, BookOpen, Users, Star, ArrowRight, PlayCircle, Clock } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import BookCard from '../components/BookCard';
import { useAppContext } from '../context/AppContext';

const Home = () => {
  const [searchTerm, setSearchTerm] = useState('');
  const navigate = useNavigate();
  const { books } = useAppContext();
  const featuredBooks = books.slice(0, 4);

  const handleSearch = (e) => {
    e.preventDefault();
    if (searchTerm.trim()) {
      navigate(`/books?search=${encodeURIComponent(searchTerm)}`);
    } else {
      navigate('/books');
    }
  };

  return (
    <div className="flex-grow-1 animate-fade-in">
      {/* Majestic Modern Hero Section */}
      <div className="bg-hero-gradient text-white py-5 position-relative">
        <Container className="py-5 position-relative z-1">
          <Row className="align-items-center mb-0 mb-lg-5">
            <Col lg={7} className="text-center text-lg-start z-1 mb-5 mb-lg-0">
              <Badge bg="white" text="dark" className="px-3 py-2 rounded-pill mb-4 fw-bold shadow-sm" style={{ color: 'var(--hero-purple)' }}>
                ✨ The Next Generation Library
              </Badge>
              <h1 className="display-3 fw-bold mb-4 lh-tight">
                Your Virtual Reading <span style={{ color: 'var(--secondary-purple)' }}>Sanctuary</span>
              </h1>
              <p className="lead mb-5 fw-light opacity-75 pe-lg-5" style={{ fontSize: '1.25rem' }}>
                Join thousands of readers accessing an infinite universe of premium books, articles, and audiobooks from anywhere.
              </p>
              
              <Form onSubmit={handleSearch} className="d-flex align-items-center bg-white p-2 rounded-pill shadow-lg mx-auto mx-lg-0" style={{ maxWidth: '500px' }}>
                <div className="ps-3 pe-2 text-muted">
                  <Search size={22} />
                </div>
                <Form.Control
                  type="text"
                  placeholder="Titles, authors, or genres..."
                  className="border-0 shadow-none bg-transparent rounded-pill fs-5 py-2 px-1"
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  style={{ fontWeight: 400 }}
                />
                <Button type="submit" className="btn-purple rounded-pill px-4 py-2 ms-2 fw-bold text-nowrap shadow-none">
                  Search
                </Button>
              </Form>
              

            </Col>
            
            <Col lg={5} className="d-none d-lg-block z-1">
              <div className="position-relative animate-float">
                <div className="glass-panel p-3 rounded-4 shadow-lg transform-rotate" style={{ transform: 'rotate(5deg)' }}>
                  <img src="https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&q=80&w=600" alt="Reading" className="img-fluid rounded-4 shadow" />
                </div>
              </div>
            </Col>
          </Row>
        </Container>
      </div>
      {/* Stats Section */}
      <section id="how-it-works" className="py-5 bg-white border-bottom">
        <Container>
          <Row className="text-center gy-4">
            <Col md={4}>
              <div className="d-flex flex-column align-items-center">
                <div className="bg-purple text-white rounded-circle d-flex align-items-center justify-content-center mb-3" style={{ width: '80px', height: '80px' }}>
                  <BookOpen size={40} />
                </div>
                <h2 className="fw-bold mb-1">10,000+</h2>
                <p className="text-muted mb-0">Digital Books</p>
              </div>
            </Col>
            <Col md={4}>
              <div className="d-flex flex-column align-items-center">
                <div className="bg-purple text-white rounded-circle d-flex align-items-center justify-content-center mb-3" style={{ width: '80px', height: '80px' }}>
                  <Users size={40} />
                </div>
                <h2 className="fw-bold mb-1">50,000+</h2>
                <p className="text-muted mb-0">Active Readers</p>
              </div>
            </Col>
            <Col md={4}>
              <div className="d-flex flex-column align-items-center">
                <div className="bg-purple text-white rounded-circle d-flex align-items-center justify-content-center mb-3" style={{ width: '80px', height: '80px' }}>
                  <Clock size={40} />
                </div>
                <h2 className="fw-bold mb-1">24/7</h2>
                <p className="text-muted mb-0">Access</p>
              </div>
            </Col>
          </Row>
        </Container>
      </section>

      {/* Featured Books Section */}
      <section className="py-5 bg-light">
        <Container className="py-4">
          <div className="text-center mb-5">
            <h2 className="fw-bold mb-2">Featured Books</h2>
            <p className="text-muted">Discover our hand-picked selection of must-read books</p>
          </div>
          
          <Row className="g-4 mb-5">
            {featuredBooks.map(book => (
              <Col key={book.id} xs={12} sm={6} lg={3}>
                <BookCard book={book} />
              </Col>
            ))}
          </Row>
          
          <div className="text-center">
            <Button onClick={() => navigate('/books')} className="btn-purple px-4 py-2 fs-6">
              View All Books
            </Button>
          </div>
        </Container>
      </section>
    </div>
  );
};

export default Home;
