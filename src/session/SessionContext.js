// SessionContext.js
import { createContext } from 'react';

const SessionContext = createContext({
  isAuthenticated: false,
  user: null,
  login: () => {},
  logout: () => {},
  checkSession: () => {},
  loading: true
});

export default SessionContext;