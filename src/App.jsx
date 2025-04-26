import { BrowserRouter, Routes, Route } from 'react-router-dom';
import HomePage from './home-page/HomePage';
import LoginForm from './authen/LoginForm';
import RegisterForm from './authen/RegisterForm';
import Dashboard from './main/Dashboard';
import ProtectedRoute from './session/ProtectedRoute';
import Room from './main/Room';
import ErrorBoundary from './utility/Errorboundary'
import CreateRoomPage from './main/CreateRoom';


function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<HomePage />} />
        <Route path="/login" element={<LoginForm />} />
        <Route path="/register" element={<RegisterForm/>} />
        <Route path="/dashboard" element={
          <ProtectedRoute>
            <Dashboard />
          </ProtectedRoute>
          } />
          <Route path="/Room/:roomId" element={
            <ProtectedRoute>
              <ErrorBoundary>
                <Room/>
              </ErrorBoundary>
              
            </ProtectedRoute>
          } />
          <Route path="/create-room" element={
          <ProtectedRoute>
            <CreateRoomPage />
          </ProtectedRoute>
          } />
        
      </Routes>
    </BrowserRouter>
  );
}

export default App
