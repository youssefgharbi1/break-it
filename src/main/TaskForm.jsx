import { useState } from 'react';
import styles from './TaskForm.module.css';

const TaskForm = ({ familyMembers, onSubmit, onCancel }) => {
  const [taskData, setTaskData] = useState({
    title: '',
    description: '',
    status: 'pending',
    priority: 'medium',
    category: '',
    assigned_to: '',
    start_time: '',
    due_time: '',
    estimated_duration: '',
    recurring_pattern: '',
    points_value: 1
  });

  const handleChange = (e) => {
    const { name, value } = e.target;
    setTaskData(prev => ({ ...prev, [name]: value }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    onSubmit(taskData);
  };

  return (
    <div className={styles.taskFormContainer}>
      <h2>Create New Task</h2>
      <form onSubmit={handleSubmit} className={styles.taskForm}>
        <div className={styles.formGroup}>
          <label>Title *</label>
          <input
            type="text"
            name="title"
            value={taskData.title}
            onChange={handleChange}
            required
          />
        </div>
        
        <div className={styles.formGroup}>
          <label>Description</label>
          <textarea
            name="description"
            value={taskData.description}
            onChange={handleChange}
          />
        </div>
        
        <div className={styles.formRow}>
          <div className={styles.formGroup}>
            <label>Status</label>
            <select
              name="status"
              value={taskData.status}
              onChange={handleChange}
            >
              <option value="pending">Pending</option>
              <option value="in-progress">In Progress</option>
              <option value="completed">Completed</option>
            </select>
          </div>
          
          <div className={styles.formGroup}>
            <label>Priority</label>
            <select
              name="priority"
              value={taskData.priority}
              onChange={handleChange}
            >
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
            </select>
          </div>
        </div>
        
        <div className={styles.formGroup}>
          <label>Category</label>
          <input
            type="text"
            name="category"
            value={taskData.category}
            onChange={handleChange}
            list="categories"
          />
          <datalist id="categories">
            <option value="Household" />
            <option value="Work" />
            <option value="Personal" />
            <option value="Family" />
          </datalist>
        </div>
        
        <div className={styles.formRow}>
          <div className={styles.formGroup}>
            <label>Assigned To</label>
            <select
              name="assigned_to"
              value={taskData.assigned_to}
              onChange={handleChange}
            >
              <option value="">Select member</option>
              {familyMembers.map(member => (
                <option key={member.id} value={member.id}>
                  {member.name}
                </option>
              ))}
            </select>
          </div>
          
          <div className={styles.formGroup}>
            <label>Points Value</label>
            <input
              type="number"
              name="points_value"
              value={taskData.points_value}
              onChange={handleChange}
              min="1"
            />
          </div>
        </div>
        
        <div className={styles.formRow}>
          <div className={styles.formGroup}>
            <label>Start Time</label>
            <input
              type="datetime-local"
              name="start_time"
              value={taskData.start_time}
              onChange={handleChange}
            />
          </div>
          
          <div className={styles.formGroup}>
            <label>Due Time</label>
            <input
              type="datetime-local"
              name="due_time"
              value={taskData.due_time}
              onChange={handleChange}
            />
          </div>
        </div>
        
        <div className={styles.formGroup}>
          <label>Estimated Duration (minutes)</label>
          <input
            type="number"
            name="estimated_duration"
            value={taskData.estimated_duration}
            onChange={handleChange}
            min="1"
          />
        </div>
        
        <div className={styles.formGroup}>
          <label>Recurring Pattern</label>
          <select
            name="recurring_pattern"
            value={taskData.recurring_pattern}
            onChange={handleChange}
          >
            <option value="">None</option>
            <option value="daily">Daily</option>
            <option value="weekly">Weekly</option>
            <option value="monthly">Monthly</option>
          </select>
        </div>
        
        <div className={styles.formActions}>
          <button type="submit" className={styles.submitButton}>
            Create Task
          </button>
          <button 
            type="button" 
            onClick={onCancel}
            className={styles.cancelButton}
          >
            Cancel
          </button>
        </div>
      </form>
    </div>
  );
};

export default TaskForm;