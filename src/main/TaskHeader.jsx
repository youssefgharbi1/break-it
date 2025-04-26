import PropTypes from 'prop-types';
import styles from './Task.module.css';

const TaskHeader = ({ task, isExpanded, toggleStatus, toggleExpand, onEdit, onDelete }) => {
  const priorityColors = {
    high: '#ff6b6b',
    medium: '#ffd166',
    low: '#06d6a0'
  };

  const handleKeyPress = (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      toggleExpand();
    }
  };

  return (
    <div className={styles.taskHeader}>
      <div 
        className={styles.taskTitle} 
        onClick={toggleExpand}
        onKeyPress={handleKeyPress}
        role="button"
        tabIndex={0}
        aria-expanded={isExpanded}
        aria-label={`${task.title} - ${task.status}`}
      >
        <input
          type="checkbox"
          checked={task.status === 'completed'}
          onChange={toggleStatus}
          className={styles.taskCheckbox}
          aria-label={`Mark task ${task.title} as ${task.status === 'completed' ? 'incomplete' : 'complete'}`}
        />
        <h3 className={task.status === 'completed' ? styles.completed : ''}>
          {task.title}
        </h3>
        <span 
          className={styles.priorityBadge}
          style={{ backgroundColor: priorityColors[task.priority.toLowerCase()] }}
          role="status"
          aria-label={`Priority: ${task.priority}`}
        >
          {task.priority}
        </span>
      </div>
      <div className={styles.taskActions}>
        <button 
          onClick={onEdit}
          className={styles.editButton}
          aria-label={`Edit task: ${task.title}`}
        >
          Edit
        </button>
        <button 
          onClick={onDelete}
          className={styles.deleteButton}
          aria-label={`Delete task: ${task.title}`}
        >
          Delete
        </button>
      </div>
    </div>
  );
};

TaskHeader.propTypes = {
  task: PropTypes.shape({
    title: PropTypes.string.isRequired,
    status: PropTypes.string.isRequired,
    priority: PropTypes.string.isRequired
  }).isRequired,
  isExpanded: PropTypes.bool.isRequired,
  toggleStatus: PropTypes.func.isRequired,
  toggleExpand: PropTypes.func.isRequired,
  onEdit: PropTypes.func.isRequired,
  onDelete: PropTypes.func.isRequired
};

export default TaskHeader;