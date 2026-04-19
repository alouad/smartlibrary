import React, { useState } from 'react';
import { Container, Row, Col, Form, Button } from 'react-bootstrap';
import { Link, useNavigate } from 'react-router-dom';
import { BookOpen, UserPlus, Mail, Lock, User, CheckCircle } from 'lucide-react';
import { useAppContext } from '../context/AppContext';

const Register = () => {
  const navigate = useNavigate();
  const { login } = useAppContext();
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [isAuthor, setIsAuthor] = useState(false);

  const handleSubmit = (e) => {
    e.preventDefault();
    login({ name: name || 'Reader', email, role: isAuthor ? 'author' : 'reader', level: 5 });
    navigate('/dashboard');
  };

  return (
    <div className="min-vh-100 d-flex align-items-center justify-content-center bg-purple-gradient py-5 animate-fade-in">
      <Container>
        <Row className="justify-content-center">
          <Col md={8} lg={6} xl={5}>
            <div className="glass-panel rounded-4 p-4 p-md-5 animate-slide-up">
              <div className="text-center mb-4">
                <div className="bg-white text-purple p-3 rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm">
                  <BookOpen size={32} />
                </div>
                <h2 className="fw-bold mb-1 text-dark">Create Account</h2>
                <p className="text-muted">Join the SmartLibrary community</p>
              </div>

              <Form onSubmit={handleSubmit}>
                <Form.Group className="mb-3">
                  <Form.Label className="form-label d-flex align-items-center gap-2">
                    <User size={16} /> Full Name
                  </Form.Label>
                  <Form.Control 
                    type="text" 
                    placeholder="John Doe" 
                    className="custom-input"
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                    required
                  />
                </Form.Group>

                <Form.Group className="mb-3">
                  <Form.Label className="form-label d-flex align-items-center gap-2">
                    <Mail size={16} /> Email Address
                  </Form.Label>
                  <Form.Control 
                    type="email" 
                    placeholder="name@example.com" 
                    className="custom-input"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    required
                  />
                </Form.Group>

                <Form.Group className="mb-3">
                  <Form.Label className="form-label d-flex align-items-center gap-2">
                    <Lock size={16} /> Password
                  </Form.Label>
                  <Form.Control 
                    type="password" 
                    placeholder="••••••••" 
                    className="custom-input"
                    required
                  />
                </Form.Group>

                <Form.Group className="mb-4">
                  <Form.Label className="form-label d-flex align-items-center gap-2">
                    <Lock size={16} /> Confirm Password
                  </Form.Label>
                  <Form.Control 
                    type="password" 
                    placeholder="••••••••" 
                    className="custom-input"
                    required
                  />
                </Form.Group>

                <Form.Group className="mb-4 d-flex align-items-center gap-2">
                  <Form.Check 
                    type="checkbox" 
                    id="author-check" 
                    onChange={(e) => setIsAuthor(e.target.checked)}
                    checked={isAuthor}
                  />
                  <Form.Label htmlFor="author-check" className="form-label mb-0 ms-1 d-flex align-items-center gap-2 text-muted fw-medium">
                    <CheckCircle size={16} /> Register as an Author
                  </Form.Label>
                </Form.Group>

                <Button type="submit" className="btn-purple w-100 py-3 mb-4 d-flex justify-content-center align-items-center gap-2 fs-5">
                  <UserPlus size={20} /> Register
                </Button>

                <div className="text-center text-muted">
                  Already have an account?{' '}
                  <Link to="/login" className="text-purple fw-bold text-decoration-none">
                    Log in here
                  </Link>
                </div>
              </Form>
            </div>
          </Col>
        </Row>
      </Container>
    </div>
  );
};

export default Register;
