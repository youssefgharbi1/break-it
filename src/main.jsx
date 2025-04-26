import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './app';
import './index.css';
import SessionProvider from './session/SessionProvider';



const root = ReactDOM.createRoot(document.getElementById('root'));
root.render(
  <React.StrictMode>
    <SessionProvider>
      <App />
    </SessionProvider>
  </React.StrictMode>
);