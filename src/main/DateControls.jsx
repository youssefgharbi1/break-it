import { useState } from 'react';
import DatePicker from 'react-datepicker';
import 'react-datepicker/dist/react-datepicker.css';
import styles from './Room.module.css';

const formatDate = (date) => {
  return date.toLocaleDateString('en-US', { 
    weekday: 'short', 
    month: 'short', 
    day: 'numeric' 
  });
};

const DateControls = ({ selectedDate, onDateChange }) => {
  const [showDatePicker, setShowDatePicker] = useState(false);

  const handleDateChange = (date) => {
    onDateChange(date);
    setShowDatePicker(false);
  };

  const navigateDate = (days) => {
    const newDate = new Date(selectedDate);
    newDate.setDate(newDate.getDate() + days);
    onDateChange(newDate);
  };

  return (
    <div className={styles.dateControls}>
      <button 
        onClick={() => navigateDate(-1)}
        className={styles.dateNavButton}
      >
        &lt;
      </button>
      
      <button 
        onClick={() => setShowDatePicker(!showDatePicker)}
        className={styles.dateDisplayButton}
      >
        {formatDate(selectedDate)}
      </button>
      
      {showDatePicker && (
        <div className={styles.datePickerPopup}>
          <DatePicker
            selected={selectedDate}
            onChange={handleDateChange}
            inline
          />
        </div>
      )}
      
      <button 
        onClick={() => navigateDate(1)}
        className={styles.dateNavButton}
      >
        &gt;
      </button>
      
      <button 
        onClick={() => onDateChange(new Date())}
        className={styles.todayButton}
      >
        Today
      </button>
    </div>
  );
};

export default DateControls;