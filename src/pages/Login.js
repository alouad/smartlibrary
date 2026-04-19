import React, { useState } from 'react';
import { Container, Row, Col, Form, Button } from 'react-bootstrap';
import { Link, useNavigate } from 'react-router-dom';
import { BookOpen, LogIn, Mail, Lock } from 'lucide-react';
import { useAppContext } from '../context/AppContext';

const Login = () => {
  const navigate = useNavigate();
  const { login } = useAppContext();
  const [email, setEmail] = useState('');

  const handleSubmit = (e) => {
    e.preventDefault();
    const namePart = email.split('@')[0];
    const formattedName = namePart.charAt(0).toUpperCase() + namePart.slice(1);
    login({ name: formattedName || 'Reader', level: 5 });
    navigate('/dashboard');
  };

  return (
    <div className="min-vh-100 d-flex align-items-center justify-content-center bg-purple-gradient py-5 animate-fade-in">
      <Container>
        <Row className="justify-content-center">
          <Col md={8} lg={6} xl={5}>
            <div className="glass-panel rounded-4 p-4 p-md-5 animate-slide-up">
              <div className="text-center mb-4">
                <div className="bg-purple text-white p-3 rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm">
                  <BookOpen size={32} />
                </div>
                <h2 className="fw-bold mb-1">Welcome Back</h2>
                <p className="text-muted">Sign in to your SmartLibrary account</p>
              </div>

              <Form onSubmit={handleSubmit}>
                <Form.Group className="mb-4">
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

                <Form.Group className="mb-4">
                  <div className="d-flex justify-content-between align-items-center mb-2">
                    <Form.Label className="form-label d-flex align-items-center gap-2 mb-0">
                      <Lock size={16} /> Password
                    </Form.Label>
                    <Link to="/forgot-password" className="text-purple text-decoration-none small">Forgot password?</Link>
                  </div>
                  <Form.Control 
                    type="password" 
                    placeholder="••••••••" 
                    className="custom-input"
                    required
                  />
                </Form.Group>

                <Button type="submit" className="btn-purple w-100 py-3 mb-4 d-flex justify-content-center align-items-center gap-2 fs-5">
                  <LogIn size={20} /> Sign In
                </Button>

                <div className="text-center text-muted">
                  Don't have an account?{' '}
                  <Link to="/register" className="text-purple fw-bold text-decoration-none">
                    Register here
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

export default Login;
