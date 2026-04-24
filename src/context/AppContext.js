import React, { createContext, useState, useContext } from 'react';
import { booksData as mockBooks, initialUsersData } from '../data/mockData';

const AppContext = createContext();

export const AppProvider = ({ children }) => {
  const [user, setUser] = useState(() => {
    const saved = localStorage.getItem('user');
    return saved ? JSON.parse(saved) : null;
  });
  const [usersList, setUsersList] = useState(() => {
    const saved = localStorage.getItem('usersList');
    return saved ? JSON.parse(saved) : initialUsersData;
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

  const login = (userData) => {
    const existingUser = usersList.find(u => u.email === userData.email);
    let data;
    if (existingUser) {
      data = existingUser;
    } else {
      data = { 
        ...userData, 
        role: userData.role || 'user', 
        isPremium: false, 
        joinDate: new Date().toISOString().split('T')[0],
        id: Date.now() 
      };
      const newUsersList = [...usersList, data];
      setUsersList(newUsersList);
      localStorage.setItem('usersList', JSON.stringify(newUsersList));
    }
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
      
      // Update in usersList
      const newUsersList = usersList.map(u => u.email === user.email ? updatedUser : u);
      setUsersList(newUsersList);
      localStorage.setItem('usersList', JSON.stringify(newUsersList));
    }
  };

  const upgradeToPremium = () => {
    if (user) {
      const updatedUser = { ...user, isPremium: true };
      setUser(updatedUser);
      localStorage.setItem('user', JSON.stringify(updatedUser));

      // Update in usersList
      const newUsersList = usersList.map(u => u.email === user.email ? updatedUser : u);
      setUsersList(newUsersList);
      localStorage.setItem('usersList', JSON.stringify(newUsersList));
    }
  };

  const adminUpdateUser = (targetEmail, updates) => {
    if (user && user.role === 'admin') {
      const newUsersList = usersList.map(u => u.email === targetEmail ? { ...u, ...updates } : u);
      setUsersList(newUsersList);
      localStorage.setItem('usersList', JSON.stringify(newUsersList));
    }
  };

  const adminDeleteUser = (targetEmail) => {
    if (user && user.role === 'admin') {
      const newUsersList = usersList.filter(u => u.email !== targetEmail);
      setUsersList(newUsersList);
      localStorage.setItem('usersList', JSON.stringify(newUsersList));
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
    <AppContext.Provider value={{ 
      user, usersList, login, logout, becomeAuthor, upgradeToPremium, adminUpdateUser, adminDeleteUser,
      favorites, toggleFavorite, bookmarks, saveBookmark, reviews, addReview, books, addBook 
    }}>
      {children}
    </AppContext.Provider>
  );
};

export const useAppContext = () => useContext(AppContext);
