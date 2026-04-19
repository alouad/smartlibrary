import React, { createContext, useState, useContext } from 'react';
import { booksData as mockBooks } from '../data/mockData';

const AppContext = createContext();

export const AppProvider = ({ children }) => {
  const [user, setUser] = useState(() => {
    const saved = localStorage.getItem('user');
    return saved ? JSON.parse(saved) : null;
  });
  const [favorites, setFavorites] = useState(() => {
    const saved = localStorage.getItem('favorites');
    return saved ? JSON.parse(saved) : [3, 7];
  });
  const [bookmarks, setBookmarks] = useState(() => {
    const saved = localStorage.getItem('bookmarks');
    return saved ? JSON.parse(saved) : {};
  });
  const [reviews, setReviews] = useState(() => {
    const saved = localStorage.getItem('reviews');
    return saved ? JSON.parse(saved) : {};
  });
  const [books, setBooks] = useState(() => {
    const saved = localStorage.getItem('books');
    return saved ? JSON.parse(saved) : mockBooks;
  });

  // Simplified auth mock logic
  const login = (userData) => {
    const data = userData || { name: 'Demo User', level: 5 };
    setUser(data);
    localStorage.setItem('user', JSON.stringify(data));
  };
  
  const logout = () => {
    setUser(null);
    localStorage.removeItem('user');
  };

  const becomeAuthor = () => {
    if (user) {
      const updatedUser = { ...user, role: 'author' };
      setUser(updatedUser);
      localStorage.setItem('user', JSON.stringify(updatedUser));
    }
  };
  
  // Favorites toggle
  const toggleFavorite = (bookId) => {
    setFavorites(prev => {
      const newFavs = prev.includes(bookId) ? prev.filter(id => id !== bookId) : [...prev, bookId];
      localStorage.setItem('favorites', JSON.stringify(newFavs));
      return newFavs;
    });
  };

  // Save Bookmark progress
  const saveBookmark = (bookId, page) => {
    setBookmarks(prev => {
      const newBookmarks = { ...prev, [bookId]: page };
      localStorage.setItem('bookmarks', JSON.stringify(newBookmarks));
      return newBookmarks;
    });
  };

  // Add Book
  const addBook = (book) => {
    setBooks(prev => {
      const newBooks = [...prev, book];
      localStorage.setItem('books', JSON.stringify(newBooks));
      return newBooks;
    });
  };

  // Add Review
  const addReview = (bookId, review) => {
    setReviews(prev => {
      const newReviews = { ...prev, [bookId]: [...(prev[bookId] || []), review] };
      localStorage.setItem('reviews', JSON.stringify(newReviews));
      return newReviews;
    });
  };

  return (
    <AppContext.Provider value={{ user, login, logout, becomeAuthor, favorites, toggleFavorite, bookmarks, saveBookmark, reviews, addReview, books, addBook }}>
      {children}
    </AppContext.Provider>
  );
};

export const useAppContext = () => useContext(AppContext);
