import React, { useState } from 'react';
import { Container, Button, ProgressBar, Modal } from 'react-bootstrap';
import { ArrowLeft, Settings, ChevronLeft, ChevronRight, Bookmark } from 'lucide-react';
import { useParams, useNavigate } from 'react-router-dom';
import { useAppContext } from '../context/AppContext';

const Reader = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const { user, bookmarks, saveBookmark, books } = useAppContext();
  const book = books.find(b => b.id === parseInt(id)) || books[0];

  React.useEffect(() => {
    if (book && book.isPremium) {
      if (!user || (!user.isPremium && user.role !== 'admin')) {
        navigate(`/book/${book.id}`);
      }
    }
  }, [book, user, navigate]);

  const [page, setPage] = useState(bookmarks[parseInt(id)] || 1);
  const totalPages = book.pages || 300;
  
  // Settings State
  const [showSettings, setShowSettings] = useState(false);
  const [theme, setTheme] = useState(() => localStorage.getItem('readerTheme') || 'light');
  const [fontSize, setFontSize] = useState(() => localStorage.getItem('readerFontSize') || 'medium');

  const handleSetTheme = (newTheme) => {
    setTheme(newTheme);
    localStorage.setItem('readerTheme', newTheme);
  };

  const handleSetFontSize = (newSize) => {
    setFontSize(newSize);
    localStorage.setItem('readerFontSize', newSize);
  };

  const handleNext = () => setPage(p => Math.min(p + 1, totalPages));
  const handlePrev = () => setPage(p => Math.max(p - 1, 1));

  const getThemeStyles = () => {
    switch(theme) {
      case 'dark': return { backgroundColor: '#1a1a1a', color: '#e0e0e0' };
      case 'sepia': return { backgroundColor: '#f4ecd8', color: '#433422' };
      case 'light':
      default: return { backgroundColor: '#ffffff', color: '#333333' };
    }
  };

  const getFontSize = () => {
    switch(fontSize) {
      case 'small': return '1rem';
      case 'large': return '1.5rem';
      case 'medium':
      default: return '1.2rem';
    }
  };

  return (
    <div className="bg-light min-vh-100 d-flex flex-column animate-fade-in">
      {/* Reader Toolbar */}
      <div className="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center shadow-sm sticky-top">
        <div className="d-flex align-items-center gap-3">
          <Button variant="link" className="text-dark p-0 me-3" onClick={() => navigate(-1)}>
            <ArrowLeft size={24} />
          </Button>
          <div className="d-none d-md-block">
            <h5 className="mb-0 fw-bold">{book.title}</h5>
            <small className="text-muted">{book.author}</small>
          </div>
        </div>
        <div className="d-flex gap-3">
          <Button 
            variant="light" 
            className="rounded-circle p-2" 
            title="Bookmark"
            onClick={() => saveBookmark(parseInt(id), page)}
          >
            <Bookmark 
              size={20} 
              className={bookmarks[parseInt(id)] === page ? "text-purple" : "text-muted"} 
              fill={bookmarks[parseInt(id)] === page ? "var(--primary-purple)" : "none"} 
            />
          </Button>
          <Button variant="light" className="rounded-circle text-muted p-2" title="Appearance Settings" onClick={() => setShowSettings(true)}>
            <Settings size={20} />
          </Button>
        </div>
      </div>

      {/* Reader Content Area */}
      <Container className="flex-grow-1 d-flex flex-column justify-content-center py-4 py-md-5" style={{ maxWidth: '800px' }}>
        <div className="p-4 p-md-5 rounded-4 shadow-sm position-relative animate-slide-up" style={{ minHeight: '60vh', fontSize: getFontSize(), lineHeight: '1.8', ...getThemeStyles(), transition: 'all 0.3s ease' }}>
          <div className="d-flex justify-content-between mb-4">
            <h4 className="text-purple fw-bold mb-0">Chapter {page}</h4>
          </div>
          {book.pdfUrl ? (
            <div style={{ height: '70vh' }}>
              <iframe src={book.pdfUrl} title={book.title} width="100%" height="100%" style={{ border: 'none' }} />
            </div>
          ) : book.content ? (
            <div style={{ whiteSpace: 'pre-wrap', wordBreak: 'break-word', overflowWrap: 'break-word' }}>
              {book.content}
            </div>
          ) : (
            <>
              <p>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
              </p>
              <p>
                Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
              </p>
              <p>
                This is a mock book reader view for <strong>{book.title}</strong> by {book.author}. The actual text content would be dynamically loaded from a database or an e-pub file context into this responsive reading pane.
              </p>
              <p>
                Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit.
              </p>
            </>
          )}
        </div>
      </Container>

      {/* Pagination Footer */}
      <div className="bg-white border-top p-3 mt-auto">
        <Container className="d-flex align-items-center justify-content-between" style={{ maxWidth: '800px' }}>
          <Button variant="outline-secondary" className="border-0 rounded-circle p-2 hover-lift" onClick={handlePrev} disabled={page === 1}>
            <ChevronLeft size={24} />
          </Button>
          
          <div className="flex-grow-1 mx-4 text-center">
            <ProgressBar variant="purple" now={(page/totalPages)*100} style={{ height: '6px' }} className="mb-2" />
            <span className="small text-muted fw-medium">Page {page} of {totalPages}</span>
          </div>

          <Button variant="outline-secondary" className="border-0 rounded-circle p-2 hover-lift" onClick={handleNext} disabled={page === totalPages}>
            <ChevronRight size={24} />
          </Button>
        </Container>
      </div>

      {/* Settings Modal */}
      <Modal show={showSettings} onHide={() => setShowSettings(false)} centered size="sm">
        <Modal.Header closeButton className="border-0 pb-0">
          <Modal.Title className="fw-bold fs-5">Appearance</Modal.Title>
        </Modal.Header>
        <Modal.Body className="p-4">
          <div className="mb-4">
            <label className="fw-medium mb-2 d-block small text-muted">Theme</label>
            <div className="d-flex gap-2">
              <Button variant="light" className="flex-grow-1" style={theme === 'light' ? {backgroundColor: '#6c5ce7', color: 'white'} : {}} onClick={() => handleSetTheme('light')}>Light</Button>
              <Button variant="light" className="flex-grow-1" style={theme === 'dark' ? {backgroundColor: '#6c5ce7', color: 'white'} : {}} onClick={() => handleSetTheme('dark')}>Dark</Button>
              <Button variant="light" className="flex-grow-1" style={theme === 'sepia' ? {backgroundColor: '#6c5ce7', color: 'white'} : {}} onClick={() => handleSetTheme('sepia')}>Sepia</Button>
            </div>
          </div>
          <div>
            <label className="fw-medium mb-2 d-block small text-muted">Text Size</label>
            <div className="d-flex gap-2">
              <Button variant="light" className="flex-grow-1" style={fontSize === 'small' ? {backgroundColor: '#6c5ce7', color: 'white'} : {}} onClick={() => handleSetFontSize('small')}>Aa</Button>
              <Button variant="light" className="flex-grow-1 fs-5 py-1" style={fontSize === 'medium' ? {backgroundColor: '#6c5ce7', color: 'white'} : {}} onClick={() => handleSetFontSize('medium')}>Aa</Button>
              <Button variant="light" className="flex-grow-1 fs-4 py-0" style={fontSize === 'large' ? {backgroundColor: '#6c5ce7', color: 'white'} : {}} onClick={() => handleSetFontSize('large')}>Aa</Button>
            </div>
          </div>
        </Modal.Body>
      </Modal>
    </div>
  );
};

export default Reader;
