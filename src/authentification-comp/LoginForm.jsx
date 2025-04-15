import React, { useState } from 'react';
import InputField from './InputField';
import Button from './Button';
import styles from './LoginForm.module.css';
import AppHeader from '../AppHeader';

const LoginForm = () => {
  const [formData, setFormData] = useState({
    email: '',
    password: '',
  });
  const [errors, setErrors] = useState({});
  const [loading, setLoading] = useState(false);
  const [loginSuccess, setLoginSuccess] = useState(false);

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
    // Add your submission logic here
  };
  // Temporary simplified test


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
                    <a href="#" className={styles.forgotPassword}>
                        Forgot password?
                    </a>
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