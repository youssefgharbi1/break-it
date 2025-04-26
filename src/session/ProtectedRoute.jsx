// ProtectedRoute.jsx
import { useContext } from 'react';
import { Navigate } from 'react-router-dom';
import SessionContext from './SessionContext';

const ProtectedRoute = ({ children }) => {
  const session = useContext(SessionContext);

  if (!session || !session.isAuthenticated) {
    return <Navigate to="/login" replace />;
  }
  

  return children;
};

export default ProtectedRoute;
