import React from 'react';
import { Navbar, Nav, Container, Button } from 'react-bootstrap';
import { NavLink, Link, useNavigate } from 'react-router-dom';
import { BookOpen, LogIn, UserPlus, User as UserIcon, LogOut } from 'lucide-react';
import { useAppContext } from '../context/AppContext';

const Navigation = () => {
  const { user, logout } = useAppContext();
  const navigate = useNavigate();

  const handleLogout = () => {
    logout();
    navigate('/');
  };

  return (
    <Navbar expand="lg" className="border-bottom py-3 glass-nav sticky-top z-2">
      <Container>
        <Navbar.Brand as={Link} to="/" className="d-flex align-items-center">
          <div className="bg-purple text-white p-2 rounded-3 me-2 d-flex align-items-center justify-content-center" style={{ width: '40px', height: '40px' }}>
            <BookOpen size={24} />
          </div>
          <div className="d-flex flex-column">
            <span className="fw-bold fs-4 text-purple" style={{ lineHeight: '1.2' }}>SmartLibrary</span>
            <span className="text-muted" style={{ fontSize: '0.75rem', fontWeight: 500 }}>Bibliothèque Virtuelle Intelligente</span>
          </div>
        </Navbar.Brand>
        <Navbar.Toggle aria-controls="basic-navbar-nav" />
        <Navbar.Collapse id="basic-navbar-nav">
          <Nav className="ms-auto align-items-center gap-3">
            <NavLink to="/" className="nav-pill-btn">
              <BookOpen size={18} /> Home
            </NavLink>
            <NavLink to="/books" className="nav-pill-btn">
              <BookOpen size={18} /> Books
            </NavLink>

            <div className="d-flex gap-2 ms-lg-3 mt-3 mt-lg-0">
              {user ? (
                <>
                  <NavLink to="/dashboard" className="nav-pill-btn">
                    <UserIcon size={18} /> Dashboard
                  </NavLink>
                  {user.role === 'author' && (
                    <NavLink to="/author" className="nav-pill-btn text-warning border-warning">
                      <BookOpen size={18} /> Author Space
                    </NavLink>
                  )}
                  <Button variant="link" className="nav-pill-btn" onClick={handleLogout}>
                    <LogOut size={18} /> Logout
                  </Button>
                </>
              ) : (
                <>
                  <NavLink to="/login" className="nav-pill-btn">
                    <LogIn size={18} /> Login
                  </NavLink>
                  <NavLink to="/register" className="nav-pill-btn">
                    <UserPlus size={18} /> Register
                  </NavLink>
                </>
              )}
            </div>
          </Nav>
        </Navbar.Collapse>
      </Container>
    </Navbar>
  );
};

export default Navigation;
