import React from 'react';
import { Card, Badge } from 'react-bootstrap';
import { Star, Clock } from 'lucide-react';
import { useNavigate } from 'react-router-dom';

const BookCard = ({ book }) => {
  const navigate = useNavigate();

  return (
    <Card 
      className="h-100 border-0 hover-lift cursor-pointer overflow-hidden bg-white" 
      onClick={() => navigate(`/book/${book.id}`)}
      style={{ cursor: 'pointer' }}
    >
      <div className="position-relative overflow-hidden" style={{ aspectRatio: '3/4' }}>
        <Card.Img 
          variant="top" 
          src={book.cover} 
          className="w-100 h-100 book-card-img" 
          style={{ objectFit: 'cover' }} 
        />
        <div className="position-absolute bottom-0 start-0 w-100 p-3" style={{ background: 'linear-gradient(to top, rgba(0,0,0,0.8), transparent)' }}>
           <Badge bg="purple" className="rounded-pill px-3 py-1 fw-medium shadow-sm" style={{ backgroundColor: 'var(--primary-purple)' }}>
            {book.category}
          </Badge>
        </div>
      </div>
      <Card.Body className="p-4 d-flex flex-column">
        <h5 className="fw-bold mb-1 text-dark text-truncate" title={book.title}>{book.title}</h5>
        <Card.Text className="text-muted small mb-3">by {book.author}</Card.Text>
        
        <div className="mt-auto d-flex align-items-center justify-content-between pt-3 border-top" style={{ borderColor: 'var(--bg-subtle)' }}>
          <div className="d-flex align-items-center text-warning fw-bold">
            <Star size={16} fill="currentColor" className="me-1" />
            <span>{book.rating}</span>
          </div>
          <div className="d-flex align-items-center text-muted small fw-medium">
            <Clock size={14} className="me-1" />
            {book.pages}p
          </div>
        </div>
      </Card.Body>
    </Card>
  );
};

export default BookCard;
