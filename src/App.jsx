import { BrowserRouter, Routes, Route, useLocation } from 'react-router-dom';
import { AnimatePresence, motion } from 'framer-motion';
import HomePage from './home-page/HomePage';
import LoginForm from './authen/LoginForm';
import RegisterForm from './authen/RegisterForm';
import Dashboard from './main/Dashboard';
import ProtectedRoute from './session/ProtectedRoute';
import Room from './main/Room';
import ErrorBoundary from './utility/Errorboundary';
import CreateRoomPage from './main/CreateRoom';

function AnimatedRoutes() {
  const location = useLocation();

  return (
    <AnimatePresence mode="wait">
      <Routes location={location} key={location.pathname}>
        <Route path="/" element={<PageTransition><HomePage /></PageTransition>} />
        <Route path="/login" element={<PageTransition><LoginForm /></PageTransition>} />
        <Route path="/register" element={<PageTransition><RegisterForm /></PageTransition>} />
        <Route path="/dashboard" element={
          <ProtectedRoute>
            <PageTransition>
              <Dashboard />
            </PageTransition>
          </ProtectedRoute>
        } />
        <Route path="/Room/:roomId" element={
          <ProtectedRoute>
            <ErrorBoundary>
              <PageTransition>
                <Room />
              </PageTransition>
            </ErrorBoundary>
          </ProtectedRoute>
        } />
        <Route path="/create-room" element={
          <ProtectedRoute>
            <PageTransition>
              <CreateRoomPage />
            </PageTransition>
          </ProtectedRoute>
        } />
      </Routes>
    </AnimatePresence>
  );
}

function App() {
  return (
    <BrowserRouter>
      <AnimatedRoutes />
    </BrowserRouter>
  );
}

// Reusable transition component
const PageTransition = ({ children }) => (
  <motion.div
    initial={{ opacity: 0, scale: 0.95 }}
    animate={{ opacity: 1, scale: 1 }}
    exit={{ opacity: 0, scale: 1.05 }}
    transition={{ duration: 0.4 }}
  >
    {children}
  </motion.div>
);

export default App;