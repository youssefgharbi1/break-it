import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import InputField from './InputField';
import Button from './Button';
import styles from './RegisterForm.module.css';

const RegisterForm = () => {
  const [formData, setFormData] = useState({
    firstName: '',
    lastName: '',
    age: 0,
    email: '',
    phone: '',
    password: '',
    repeatPassword: ''
  });

  const [errors, setErrors] = useState({});
  const [loading, setLoading] = useState(false);
  const navigate = useNavigate();

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
    if (errors[name]) setErrors(prev => ({ ...prev, [name]: '' }));
  };

  const validateForm = () => {
    const newErrors = {};
    const phoneRegex = /^[+]?[(]?[0-9]{1,4}[)]?[-\s.]?[0-9]{1,3}[-\s.]?[0-9]{3,6}$/im;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const ageRegex = /^(?:1[01][0-9]|120|[1-9][0-9]?)$/;

    if (!formData.firstName.trim()) newErrors.firstName = 'First name is required';
    if (!formData.lastName.trim()) newErrors.lastName = 'Last name is required';
    
    if (!formData.age) newErrors.age = 'Age is required';
    else if (!ageRegex.test(formData.age)) newErrors.age = 'Invalid age';

    if (!formData.email) newErrors.email = 'Email is required';
    else if (!emailRegex.test(formData.email)) newErrors.email = 'Invalid email format';
    
    if (!formData.phone) newErrors.phone = 'Phone number is required';
    else if (!phoneRegex.test(formData.phone)) newErrors.phone = 'Invalid phone number';
    
    if (!formData.password) newErrors.password = 'Password is required';
    else if (formData.password.length < 8) newErrors.password = 'Password must be at least 8 characters';
    
    if (formData.password !== formData.repeatPassword) {
      newErrors.repeatPassword = 'Passwords do not match';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!validateForm()) return;

    setLoading(true);
    try {
      // Replace with actual API call
      await new Promise(resolve => setTimeout(resolve, 1000));
      console.log('Registration data:', {
        firstName: formData.firstName,
        lastName: formData.lastName,
        age : formData.age,
        email: formData.email,
        phone: formData.phone,
        password: formData.password

      });
      navigate('/verify-email'); // Redirect after successful registration
    } catch (error) {
      setErrors({ submit: error.message });
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className={styles.registerForm}>
      <h2 className={styles.title}>Create Your Account</h2>
      
      {errors.submit && (
        <div className={styles.errorAlert}>{errors.submit}</div>
      )}

      <form onSubmit={handleSubmit} noValidate>
        <div className={styles.nameFields}>
          <InputField
            name="firstName"
            label="First Name"
            value={formData.firstName}
            onChange={handleChange}
            error={errors.firstName}
            required
            autoFocus
          />
          <InputField
            name="lastName"
            label="Last Name"
            value={formData.lastName}
            onChange={handleChange}
            error={errors.lastName}
            required
          />
        </div>

        <InputField
            name="age"
            label="Age"
            value={formData.age}
            onChange={handleChange}
            error={errors.age}
            required
          />
        
        <InputField
          type="email"
          name="email"
          label="Email Address"
          value={formData.email}
          onChange={handleChange}
          error={errors.email}
          required
        />

        <InputField
          type="tel"
          name="phone"
          label="Phone Number"
          value={formData.phone}
          onChange={handleChange}
          error={errors.phone}
          placeholder="+1 (123) 456-7890"
          required
        />

        <InputField
          type="password"
          name="password"
          label="Password"
          value={formData.password}
          onChange={handleChange}
          error={errors.password}
          required
        />

        <InputField
          type="password"
          name="repeatPassword"
          label="Repeat Password"
          value={formData.repeatPassword}
          onChange={handleChange}
          error={errors.repeatPassword}
          required
        />

        <Button
          type="submit"
          variant="primary"
          loading={loading}
          className={styles.submitButton}
        >
          Create Account
        </Button>
      </form>

      <div className={styles.loginLink}>
        Already have an account? <Link to="/login">Sign in</Link>
      </div>
    </div>
  );
};

export default RegisterForm;