import React from 'react';
import { Container, Row, Col, Form, Button } from 'react-bootstrap';
import { Link, useNavigate } from 'react-router-dom';
import { Mail, ArrowLeft, Key } from 'lucide-react';

const ForgotPassword = () => {
  const navigate = useNavigate();

  const handleSubmit = (e) => {
    e.preventDefault();
    // In a real app, this would trigger an email.
    alert('Password reset link has been sent to your email.');
    navigate('/login');
  };

  return (
    <div className="min-vh-100 d-flex align-items-center justify-content-center bg-purple-gradient py-5 animate-fade-in">
      <Container>
        <Row className="justify-content-center">
          <Col md={8} lg={6} xl={5}>
            <div className="glass-panel rounded-4 p-4 p-md-5 animate-slide-up">
              <div className="text-center mb-4">
                <div className="bg-purple text-white p-3 rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm">
                  <Key size={32} />
                </div>
                <h2 className="fw-bold mb-1">Reset Password</h2>
                <p className="text-muted">Enter your email to receive a reset link</p>
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
                    required
                  />
                </Form.Group>

                <Button type="submit" className="btn-purple w-100 py-3 mb-4 d-flex justify-content-center align-items-center gap-2 fs-5">
                  Send Reset Link
                </Button>

                <div className="text-center text-muted">
                  <Link to="/login" className="text-purple fw-bold text-decoration-none d-flex align-items-center justify-content-center gap-2">
                    <ArrowLeft size={16} /> Back to Sign In
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

export default ForgotPassword;
