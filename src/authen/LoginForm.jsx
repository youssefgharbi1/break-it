import React, { useState } from 'react';
import InputField from '../InputField';
import Button from '../Button';
import styles from './LoginForm.module.css';
import AppHeader from '../AppHeader';
import { Link, useNavigate } from 'react-router-dom';
import { useContext } from 'react';
import SessionContext from '../session/SessionContext';

const LoginForm = () => {
  const apiUrl = import.meta.env.VITE_API_URL;
  const context = useContext(SessionContext);
  const { login } = context || {};
  const [formData, setFormData] = useState({
    email: '',
    password: '',
  });
  const [errors, setErrors] = useState({});
  const [isLoading, setIsLoading] = useState(false);
  const [apiError, setApiError] = useState('');
  const navigate = useNavigate();


  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
    
    if (errors[name]) {
      setErrors(prev => ({
        ...prev,
        [name]: null
      }));
    }
  };

  const validateForm = () => {
    const newErrors = {};
    if (!formData.email.trim()) newErrors.email = 'Email is required';
    else if (!/^\S+@\S+\.\S+$/.test(formData.email)) newErrors.email = 'Invalid email format';
    
    if (!formData.password) newErrors.password = 'Password is required';
    else if (formData.password.length < 6) newErrors.password = 'Password must be at least 6 characters';

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setApiError('');
    
    if (!validateForm()) return;
    
    setIsLoading(true);
    
    try {
      const response = await fetch(`${apiUrl}/break-it-api/public/authentification/login.php`, {
        method: 'POST',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          email: formData.email.trim(),
          password: formData.password
        }),
      });

      const responseText = await response.text();
      let data;
      
      try {
        data = JSON.parse(responseText);
      } catch (jsonError) {
        console.error('Failed to parse JSON:', responseText);
        throw new Error('Server returned invalid response');
      }

      if (!response.ok || !data.success) {
        throw new Error(data.message || 'Login failed. Please try again.');
      }

      if (login) {
        login({
          ...data.user
        });
      }
      navigate('/dashboard');
      
    } catch (error) {
      console.error('Login error:', error);
      setApiError(error.message);
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className={styles.container}>
      <div>
        <AppHeader/>
        <div className={styles.loginForm}>
          <h2 className={styles.title}>Log In</h2>
          
          {apiError && (
            <div className={styles.errorMessage}>{apiError}</div>
          )}

          <form onSubmit={handleSubmit}>
            <div className={styles.formGroup}>
              <InputField
                type="email"
                name="email"
                label="Email Address"
                value={formData.email}
                onChange={handleChange}
                error={errors.email}
                disabled={isLoading}
                required
              />
            </div>

            <div className={styles.formGroup}>
              <InputField
                type="password"
                name="password"
                label="Password"
                value={formData.password}
                onChange={handleChange}
                error={errors.password}
                disabled={isLoading}
                required
              />
              <div className={styles.linkContainer}>
                <Link to="/register" className={styles.forgotPassword}>
                  Sign up
                </Link>
                <Link to="/forgot-password" className={styles.forgotPassword}>
                  Forgot password?
                </Link>
              </div>
            </div>

            <Button
              type="submit"
              variant="primary"
              loading={isLoading}
              className={styles.submitButton}
            >
              {isLoading ? 'Logging in...' : 'Log In'}
            </Button>
          </form>
        </div>
      </div>
    </div>
  );
};

export default LoginForm;