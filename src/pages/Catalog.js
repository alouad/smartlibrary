import React, { useState, useEffect } from 'react';
import { Container, Row, Col, Form, InputGroup, Button } from 'react-bootstrap';
import { Search, Filter, Star, BookOpen } from 'lucide-react';
import { useLocation } from 'react-router-dom';
import BookCard from '../components/BookCard';
import { categories } from '../data/mockData';
import { useAppContext } from '../context/AppContext';

const Catalog = () => {
  const location = useLocation();
  const { books } = useAppContext();
  const [searchTerm, setSearchTerm] = useState('');
  const [categoryFilter, setCategoryFilter] = useState('All');
  const [ratingFilter, setRatingFilter] = useState('All');

  useEffect(() => {
    const params = new URLSearchParams(location.search);
    const search = params.get('search');
    const category = params.get('category');
    
    if (search) setSearchTerm(search);
    if (category) setCategoryFilter(category.charAt(0).toUpperCase() + category.slice(1));
  }, [location]);

  const filteredBooks = books.filter(book => {
    const matchesSearch = book.title.toLowerCase().includes(searchTerm.toLowerCase()) || 
                          book.author.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesCategory = categoryFilter === 'All' || book.category === categoryFilter;
    const matchesRating = ratingFilter === 'All' || book.rating >= parseFloat(ratingFilter);
    return matchesSearch && matchesCategory && matchesRating;
  });

  return (
    <div className="py-5">
      {/* Categories Selection */}
      <Container className="mb-5 border-bottom pb-5">
        <div className="text-center mb-5">
          <h2 className="fw-bold mb-2">Browse by Category</h2>
          <p className="text-muted">Find books in your favorite topics</p>
        </div>
        <Row className="g-3">
          {categories.map((category, idx) => (
            <Col key={idx} xs={6} md={4} lg={3}>
              <div 
                className={`rounded-3 p-4 text-center cursor-pointer h-100 d-flex flex-column justify-content-center align-items-center transition-all text-decoration-none hover-lift border-0 ${categoryFilter === category ? 'bg-purple text-white shadow-md' : 'bg-light-purple text-purple'}`}
                style={{ cursor: 'pointer' }}
                onClick={() => setCategoryFilter(category)}
              >
                <BookOpen className="mb-2" size={32} />
                <span className="fw-bold fs-5">{category}</span>
              </div>
            </Col>
          ))}
        </Row>
      </Container>

      {/* Catalog Section */}
      <Container>
        <div className="mb-4">
          <h2 className="fw-bold mb-2">Book Catalog</h2>
          <p className="text-muted">Browse our complete collection of digital books</p>
        </div>

        {/* Filters */}
        <div className="bg-light p-3 p-md-4 rounded-4 mb-4">
          <Row className="g-3">
            <Col lg={4}>
              <InputGroup className="bg-white rounded-3 shadow-sm border overflow-hidden">
                <InputGroup.Text className="bg-white border-0 text-muted">
                  <Search size={18} />
                </InputGroup.Text>
                <Form.Control 
                  placeholder="Search books or authors..." 
                  className="border-0 shadow-none ps-0"
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                />
              </InputGroup>
            </Col>
            <Col md={6} lg={4}>
              <InputGroup className="bg-white rounded-3 shadow-sm border overflow-hidden">
                <InputGroup.Text className="bg-white border-0 text-muted">
                  <Filter size={18} />
                </InputGroup.Text>
                <Form.Select 
                  className="border-0 shadow-none cursor-pointer"
                  value={categoryFilter}
                  onChange={(e) => setCategoryFilter(e.target.value)}
                >
                  <option value="All">All Categories</option>
                  {categories.map(cat => (
                    <option key={cat} value={cat}>{cat}</option>
                  ))}
                </Form.Select>
              </InputGroup>
            </Col>
            <Col md={6} lg={4}>
              <InputGroup className="bg-white rounded-3 shadow-sm border overflow-hidden">
                <InputGroup.Text className="bg-white border-0 text-muted">
                  <Star size={18} />
                </InputGroup.Text>
                <Form.Select 
                  className="border-0 shadow-none cursor-pointer"
                  value={ratingFilter}
                  onChange={(e) => setRatingFilter(e.target.value)}
                >
                  <option value="All">All Ratings</option>
                  <option value="4.5">4.5+ Stars</option>
                  <option value="4.0">4.0+ Stars</option>
                  <option value="3.0">3.0+ Stars</option>
                </Form.Select>
              </InputGroup>
            </Col>
          </Row>
        </div>

        {/* Book Grid */}
        <p className="text-muted mb-4">Showing {filteredBooks.length} books</p>
        
        {filteredBooks.length > 0 ? (
          <Row className="g-4 mb-5">
            {filteredBooks.map(book => (
              <Col key={book.id} xs={12} sm={6} lg={3}>
                <BookCard book={book} />
              </Col>
            ))}
          </Row>
        ) : (
          <div className="text-center py-5">
            <h4 className="text-muted">No books found matching your criteria.</h4>
            <Button variant="link" className="text-purple mt-2" onClick={() => { setSearchTerm(''); setCategoryFilter('All'); setRatingFilter('All'); }}>
              Clear Filters
            </Button>
          </div>
        )}
      </Container>
    </div>
  );
};

export default Catalog;
