import React from 'react';
import { Container, Row, Col } from 'react-bootstrap';
import { BookOpen, Mail, Phone, MapPin } from 'lucide-react';
import { Link } from 'react-router-dom';

const Footer = () => {
  return (
    <footer className="py-5 mt-auto text-white" style={{ backgroundColor: '#111111' }}>
      <Container>
        <Row className="gy-4">
          <Col lg={4} md={6}>
            <div className="d-flex align-items-center mb-3">
              <div className="bg-purple text-white p-2 rounded-3 me-2 d-flex align-items-center justify-content-center" style={{ width: '40px', height: '40px' }}>
                <BookOpen size={24} />
              </div>
              <span className="fw-bold fs-4 text-white">SmartLibrary</span>
            </div>
            <p className="text-light opacity-75 pe-md-4">
              Your digital gateway to endless knowledge. Access thousands of books anytime, anywhere.
            </p>
          </Col>
          
          <Col lg={2} md={6}>
            <h5 className="mb-3 text-white">Quick Links</h5>
            <ul className="list-unstyled d-flex flex-column gap-2 opacity-75">
              <li><Link to="/" className="text-light text-decoration-none">Home</Link></li>
              <li><Link to="/books" className="text-light text-decoration-none">Books</Link></li>
              <li><Link to="/categories" className="text-light text-decoration-none">Categories</Link></li>
              <li><Link to="/about" className="text-light text-decoration-none">About Us</Link></li>
            </ul>
          </Col>

          <Col lg={3} md={6}>
            <h5 className="mb-3 text-white">Categories</h5>
            <ul className="list-unstyled d-flex flex-column gap-2 opacity-75">
              <li><Link to="/books?category=fiction" className="text-light text-decoration-none">Fiction</Link></li>
              <li><Link to="/books?category=science" className="text-light text-decoration-none">Science</Link></li>
              <li><Link to="/books?category=history" className="text-light text-decoration-none">History</Link></li>
              <li><Link to="/books?category=technology" className="text-light text-decoration-none">Technology</Link></li>
            </ul>
          </Col>

          <Col lg={3} md={6}>
            <h5 className="mb-3 text-white">Contact Us</h5>
            <ul className="list-unstyled d-flex flex-column gap-3 opacity-75">
              <li className="d-flex align-items-center gap-2">
                <Mail size={16} /> info@smartlibrary.com
              </li>
              <li className="d-flex align-items-center gap-2">
                <Phone size={16} /> +1 (555) 123-4567
              </li>
              <li className="d-flex align-items-center gap-2">
                <MapPin size={16} /> 123 Library St, Book City
              </li>
            </ul>
          </Col>
        </Row>
      </Container>
    </footer>
  );
};

export default Footer;
