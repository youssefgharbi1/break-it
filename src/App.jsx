import { BrowserRouter, Routes, Route } from 'react-router-dom';
import HomePage from './home-page/HomePage';
import LoginForm from './authen/LoginForm';
import RegisterForm from './authen/RegisterForm';


function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<HomePage />} />
        <Route path="/login" element={<LoginForm />} />
        <Route path="/register" element={<RegisterForm/>} />
      </Routes>
    </BrowserRouter>
  );
}

export default App
