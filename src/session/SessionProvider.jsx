// SessionProvider.js
import React, { useState, useEffect } from 'react';
import SessionContext from './SessionContext';

const SessionProvider = ({ children }) => {
  const [session, setSession] = useState({
    isAuthenticated: false,
    user: null,
    loading: true
  });

  const login = (userData) => {
    setSession({
      isAuthenticated: true,
      user: userData,
      loading: false
    });
  };

  const logout = async () => {
    try {
      await fetch('http://localhost/break-it-api/public/authentification/logout.php', {
        method: 'POST',
        credentials: 'include'
      });
      setSession({
        isAuthenticated: false,
        user: null,
        loading: false
      });
    } catch (error) {
      console.error('Logout failed:', error);
      setSession(prev => ({
        ...prev,
        loading: false
      }));
    }
  };

  const checkSession = async () => {
    try {
      const response = await fetch('http://localhost/break-it-api/public/session.php', {
        credentials: 'include',
      });
      
      const data = await response.json();
      
      if (data.success && data.user) {
        login(data.user);
      } else {
        setSession({
          isAuthenticated: false,
          user: null,
          loading: false
        });
      }
    } catch (error) {
      console.error('Session check failed:', error);
      setSession({
        isAuthenticated: false,
        user: null,
        loading: false
      });
    }
  };

  useEffect(() => {
    checkSession();
  }, []);

  const contextValue = {
    ...session,
    login,
    logout,
    checkSession
  };

  return (
    <SessionContext.Provider value={contextValue}>
      {!session.loading && children}
    </SessionContext.Provider>
  );
};

export default SessionProvider;