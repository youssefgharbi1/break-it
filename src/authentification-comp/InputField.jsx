import React from 'react';
import styles from './InputField.module.css';

const InputField = ({
  type = "text",
  name,
  label,
  value,
  onChange,
  placeholder,
  error,
  required = false,
}) => {

  const handleChange = (e) => {
    onChange(e); 
  };

  return (
    <div className={styles.inputField}>
      {label && (
        <label className={styles.inputLabel}>
          {label}
          {required && <span className={styles.required}>*</span>}
        </label>
      )}
      <input
        type={type}
        name={name} 
        value={value || ''} 
        onChange={handleChange}
        placeholder={placeholder}
        className={`${styles.input} ${error ? styles.inputError : ""}`}
        style={{ color: '#000' }} 
      />
      {error && <p className={styles.errorMessage}>{error}</p>}
    </div>
  );
};

export default InputField;