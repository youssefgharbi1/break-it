// TaskStatusColumn.jsx
import React, { useMemo } from 'react';
import Task from './Task';
import styles from './Room.module.css';

const TaskStatusColumn = ({
  title,
  tasks,
  onUpdate,
  onDelete,
  showAssignedTo = false,
  showCreatedBy = false,
  selectedDate = new Date() // Default value
}) => {
  // Group tasks by status
  const statusGroups = useMemo(() => ({
    pending: tasks.filter(t => t.status === 'pending'),
    'in-progress': tasks.filter(t => t.status === 'in_progress'),
    completed: tasks.filter(t => t.status === 'completed')
  }), [tasks]);

  // Format selected date (optional)
  const formatDate = (date) => {
    return date.toLocaleDateString('en-US', {
      weekday: 'short',
      month: 'short',
      day: 'numeric'
    });
  };

  // JSX rendering
  return (
    <div className={styles.taskColumn}>
      <h2>{title} ({tasks.length})</h2>

      {Object.entries(statusGroups).map(([status, statusTasks]) => {
        // Dynamic class for left border based on status
        const statusClass =
          status === 'pending'
            ? styles.statusPending
            : status === 'in-progress'
            ? styles.statusInProgress
            : styles.statusCompleted;

        return (
          <div key={status} className={`${styles.statusSection} ${statusClass}`}>
            <h3>
              {status === 'pending' ? 'Pending' : 
             status === 'in-progress' ? 'In Progress' : 'Completed'} ({statusTasks.length})
            </h3>

            {statusTasks.length === 0 ? (
              <p className={styles.noTasks}>
                No {status} tasks
              </p>
            ) : (
              statusTasks.map(task => (
                <Task
                  key={task.id}
                  task={task}
                  onUpdate={onUpdate}
                  onDelete={onDelete}
                  showAssignedTo={showAssignedTo}
                  showCreatedBy={showCreatedBy}
                />
              ))
            )}
          </div>
        );
      })}
    </div>
  );
};

export default React.memo(TaskStatusColumn);
