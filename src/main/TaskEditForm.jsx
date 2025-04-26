import { useState, useEffect } from 'react';
import PropTypes from 'prop-types';
import styles from './Task.module.css';

const TaskEditForm = ({ task, onSave, onCancel }) => {
  const [editedTask, setEditedTask] = useState(task);
  const [errors, setErrors] = useState({});

  useEffect(() => {
    setEditedTask(task);
  }, [task]);

  const validateForm = () => {
    const newErrors = {};
    if (!editedTask.title.trim()) {
      newErrors.title = 'Title is required';
    }
    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    setEditedTask(prev => ({ ...prev, [name]: value }));
    // Clear error when user starts typing
    if (errors[name]) {
      setErrors(prev => ({ ...prev, [name]: undefined }));
    }
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    if (validateForm()) {
      onSave(editedTask);
    }
  };

  return (
    <form 
      className={styles.editForm}
      onSubmit={handleSubmit}
      role="form"
      aria-label="Edit task form"
    >
      <div>
        <label htmlFor="title">Title</label>
        <input
          id="title"
          type="text"
          name="title"
          value={editedTask.title}
          onChange={handleChange}
          className={`${styles.editInput} ${errors.title ? styles.errorInput : ''}`}
          aria-invalid={errors.title ? 'true' : 'false'}
          aria-describedby={errors.title ? 'titleError' : undefined}
        />
        {errors.title && (
          <span id="titleError" className={styles.errorText} role="alert">
            {errors.title}
          </span>
        )}
      </div>

      <div>
        <label htmlFor="description">Description</label>
        <textarea
          id="description"
          name="description"
          value={editedTask.description || ''}
          onChange={handleChange}
          className={styles.editTextarea}
          aria-label="Task description"
        />
      </div>

      <div className={styles.editControls}>
        <div>
          <label htmlFor="priority">Priority</label>
          <select
            id="priority"
            name="priority"
            value={editedTask.priority}
            onChange={handleChange}
            aria-label="Task priority"
          >
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
          </select>
        </div>

        <div>
          <label htmlFor="status">Status</label>
          <select
            id="status"
            name="status"
            value={editedTask.status}
            onChange={handleChange}
            aria-label="Task status"
          >
            <option value="pending">Pending</option>
            <option value="in-progress">In Progress</option>
            <option value="completed">Completed</option>
          </select>
        </div>

        <button 
          type="submit" 
          className={styles.saveButton}
          aria-label="Save task"
        >
          Save
        </button>
        <button 
          type="button"
          onClick={onCancel} 
          className={styles.cancelButton}
          aria-label="Cancel editing"
        >
          Cancel
        </button>
      </div>
    </form>
  );
};

TaskEditForm.propTypes = {
  task: PropTypes.shape({
    title: PropTypes.string.isRequired,
    description: PropTypes.string,
    status: PropTypes.oneOf(['pending', 'in-progress', 'completed']).isRequired,
    priority: PropTypes.oneOf(['low', 'medium', 'high']).isRequired
  }).isRequired,
  onSave: PropTypes.func.isRequired,
  onCancel: PropTypes.func.isRequired
};

export default TaskEditForm;