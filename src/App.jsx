import { BrowserRouter, Routes, Route } from 'react-router-dom'
import LoginForm from './authentification-comp/LoginForm'
import AppHeader from './AppHeader'

function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<div>Home Page</div>} />
        <Route path="/login" element={<LoginForm />} />
        <Route path="/test" element={<div>Home Page</div>} />
      </Routes>
    </BrowserRouter>
  )
}

export default App