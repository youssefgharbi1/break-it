import React, { useState } from 'react';
import InputField from '../InputField';
import Button from '../Button';
import styles from './LoginForm.module.css';
import AppHeader from '../AppHeader';
import { Link, useNavigate } from 'react-router-dom';

const LoginForm = () => {
  const [formData, setFormData] = useState({
    email: '',
    password: '',
  });
  const [errors, setErrors] = useState({});
  const [loading, setLoading] = useState(false);
  const [loginSuccess, setLoginSuccess] = useState(false);
  const navigate = useNavigate();

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData({
      ...formData,
      [name]: value,
    });
    if (errors[name]) {
      setErrors({
        ...errors,
        [name]: null,
      });
    }
  };

  const validateForm = () => {
    const newErrors = {};
    if (!formData.email) newErrors.email = 'Email is required';
    if (!formData.password) newErrors.password = 'Password is required';
    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    console.log('Form submitted with:', formData);
    try {
      fetch('http://localhost/break-it-api/public/login.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json', // Required for JSON data
        },
        credentials: 'include',
        body: JSON.stringify({
          email: formData.email,
          password: formData.password
        })
      }).then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          alert("login successful!");
          // Redirect to login or dashboard
          navigate("/dashboard"); 
        } else {
          // Show validation errors
          alert(data.message || "login failed"); 
          console.error(data.errors); // Log detailed errors
        }
      });
    } catch (error) {
      setErrors({ submit: error.message });
    } finally {
      setLoading(false);
    }
  };



  return (
    <div className={styles.container}>
        <div>
        <AppHeader/>
        <div className={styles.loginForm}>
            <h2 className={styles.title}>Log In</h2>
            
            {loginSuccess ? (
                <div className={styles.successMessage}>Login successful!</div>
            ) : (
                <>
                {errors.submit && (
                    <div className={styles.errorMessage}>{errors.submit}</div>
                )}

                <form onSubmit={handleSubmit}>
                    {/* Keep all your existing form fields */}
                    <div className={styles.formGroup}>
                    <InputField
                        type="email"
                        name="email"
                        label="Email Address"
                        value={formData.email}
                        onChange={handleChange}
                        error={errors.email}
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
                        required
                    />
                    <div className={styles.linkContainer}>
                      <a href="/register" className={styles.forgotPassword}>
                          sign up
                      </a>
                      <a href="#" className={styles.forgotPassword}>
                          Forgot password?
                      </a>
                    </div>
                    
                    
                    </div>

                    <Button
                    type="submit"
                    variant="primary"
                    loading={loading}
                    className={styles.submitButton}
                    onClick={validateForm}
                    >
                    Log In
                    </Button>
                </form>
                </>
            )}
        </div>
        </div>
    </div>
    
  );
};

export default LoginForm;