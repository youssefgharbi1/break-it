import styles from './RadioGroup.module.css';

const RadioGroup = ({
  name,
  label,
  options,
  value,
  onChange,
  error,
  required = false
}) => {
  return (
    <div className={styles.radioGroup}>
      {label && (
        <label className={styles.groupLabel}>
          {label}
          {required && <span className={styles.required}>*</span>}
        </label>
      )}
      
      <div className={styles.optionsContainer}>
        {options.map((option) => (
          <label key={option.value} className={styles.radioOption}>
            <input
              type="radio"
              name={name}
              value={option.value}
              checked={value === option.value}
              onChange={onChange}
              className={styles.radioInput}
            />
            <span className={styles.customRadio}></span>
            {option.label}
          </label>
        ))}
      </div>
      
      {error && <p className={styles.errorMessage}>{error}</p>}
    </div>
  );
};

export default RadioGroup;