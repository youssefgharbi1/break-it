import { BrowserRouter, Routes, Route } from 'react-router-dom';
import HomePage from './home-page/HomePage';
import LoginForm from './authen/LoginForm';
import RegisterForm from './authen/RegisterForm';
import Dashboard from './main/Dashboard';


function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<HomePage />} />
        <Route path="/login" element={<LoginForm />} />
        <Route path="/register" element={<RegisterForm/>} />
        <Route path="/dashboard" element={<Dashboard />} />
      </Routes>
    </BrowserRouter>
  );
}

export default App
