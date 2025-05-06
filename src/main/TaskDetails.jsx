import PropTypes from 'prop-types';
import styles from './Task.module.css';

const formatDateTime = (dateTimeString) => {
  if (!dateTimeString) return 'Not set';
  
  try {
    const date = new Date(dateTimeString);
    return date.toLocaleString(undefined, {
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  } catch (error) {
    console.error('Invalid date format:', error);
    return 'Invalid date';
  }
};

const TaskDetails = ({ task, isExpanded }) => {
  if (!isExpanded && !task.description) return null;

  return (
    <div 
      className={styles.taskDetails}
      role="region"
      aria-label="Task details"
    >
      {task.description && (
        <p className={styles.taskDescription}>
          {task.description}
        </p>
      )}
      <div className={styles.taskMeta} role="list">
        <span role="listitem">
          Created: {formatDateTime(task.dateCreated)}
        </span>
        {task.startTime && (
          <span role="listitem">
            Starts: {formatDateTime(task.startTime)}
          </span>
        )}
        {task.dueTime && (
          <span role="listitem">
            Due: {formatDateTime(task.dueTime)}
          </span>
        )}
        {task.estimatedDuration && (
          <span role="listitem">
            Duration: {task.estimatedDuration} mins
          </span>
        )}
        {task.category && (
          <span role="listitem">
            Category: {task.category}
          </span>
        )}
        <span role="listitem">
          Points: {task.pointsValue}
        </span>
        {task.recurringPattern && (
          <span role="listitem">
            Repeats: {task.recurringPattern}
          </span>
        )}
        {task.assignedTo && (
          <span role="listitem">
            Assigned to: {task.assignedToName}
          </span>
        )}
      </div>
    </div>
  );
};

TaskDetails.propTypes = {
  task: PropTypes.shape({
    description: PropTypes.string,
    dateCreated: PropTypes.string.isRequired,
    startTime: PropTypes.string,
    dueTime: PropTypes.string,
    estimatedDuration: PropTypes.number,
    category: PropTypes.string,
    pointsValue: PropTypes.number.isRequired,
    recurringPattern: PropTypes.string
  }).isRequired,
  isExpanded: PropTypes.bool.isRequired
};

export default TaskDetails;