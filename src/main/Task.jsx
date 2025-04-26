import { useState } from 'react';
import PropTypes from 'prop-types';
import styles from './Task.module.css';
import TaskHeader from './TaskHeader';
import TaskDetails from './TaskDetails';
import TaskEditForm from './TaskEditForm';

const Task = ({ task, onUpdate, onDelete }) => {
  const [isEditing, setIsEditing] = useState(false);
  const [isExpanded, setIsExpanded] = useState(false);

  const toggleStatus = () => {
    const newStatus = task.status === 'completed' ? 'pending' : 'completed';
    onUpdate(task.id, { status: newStatus });
  };

  const handleUpdate = (updatedTask) => {
    onUpdate(task.id, updatedTask);
    setIsEditing(false);
  };

  const handleKeyPress = (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      setIsExpanded(!isExpanded);
    }
  };

  return (
    <div 
      className={`${styles.taskCard} ${styles[task.status.replace('_', '-')] || ''}`}
      style={{ borderLeft: `4px solid ${getPriorityColor(task.priority)}` }}
      role="article"
      aria-label={`Task: ${task.title}`}
      tabIndex={0}
      onKeyPress={handleKeyPress}
    >
      {isEditing ? (
        <TaskEditForm
          task={task}
          onSave={handleUpdate}
          onCancel={() => setIsEditing(false)}
        />
      ) : (
        <>
          <TaskHeader
            task={task}
            isExpanded={isExpanded}
            toggleStatus={toggleStatus}
            toggleExpand={() => setIsExpanded(!isExpanded)}
            onEdit={() => setIsEditing(true)}
            onDelete={() => onDelete(task.id)}
          />
          <TaskDetails
            task={task}
            isExpanded={isExpanded}
          />
        </>
      )}
    </div>
  );
};

const getPriorityColor = (priority) => {
  const colors = {
    high: '#ff6b6b',
    medium: '#ffd166',
    low: '#06d6a0',
    urgent: '#ef476f'
  };
  return colors[priority.toLowerCase()] || colors.medium;
};

Task.propTypes = {
  task: PropTypes.shape({
    id: PropTypes.number.isRequired,
    title: PropTypes.string.isRequired,
    description: PropTypes.string,
    status: PropTypes.oneOf([
      'pending', 
      'in_progress', 
      'completed',
      'approved',
      'rejected',
      'archived'
    ]).isRequired,
    priority: PropTypes.oneOf([
      'low', 
      'medium', 
      'high',
      'urgent'
    ]).isRequired,
    dateCreated: PropTypes.string.isRequired,
    startTime: PropTypes.string,
    dueTime: PropTypes.string,
    estimatedDuration: PropTypes.number,
    category: PropTypes.string,
    pointsValue: PropTypes.number.isRequired,
    recurringPattern: PropTypes.string,
    assignedTo: PropTypes.number,
    createdBy: PropTypes.number,
    familyId: PropTypes.number,
    roomId: PropTypes.number,
    isApproved: PropTypes.bool,
    completionNotes: PropTypes.string
  }).isRequired,
  onUpdate: PropTypes.func.isRequired,
  onDelete: PropTypes.func.isRequired
};

export default Task;