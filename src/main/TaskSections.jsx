import TaskStatusColumn from './TaskStatusColumn';
import styles from './Room.module.css';

const TaskSections = ({ 
  user, 
  myAssignedTasks, 
  tasksAssignedToMe, 
  onTaskUpdate, 
  onTaskDelete,
  selectedDate 
}) => {
  return (
    <div className={styles.taskSections}>
      {user.role === 'P' && (
        <TaskStatusColumn
          title="Tasks I Assigned"
          tasks={myAssignedTasks}
          onUpdate={onTaskUpdate}
          onDelete={onTaskDelete}
          showAssignedTo={true}
          selectedDate={selectedDate}
        />
      )}
      
      <TaskStatusColumn
        title="My Tasks"
        tasks={tasksAssignedToMe}
        onUpdate={onTaskUpdate}
        onDelete={onTaskDelete}
        showCreatedBy={true}
        selectedDate={selectedDate}
      />
    </div>
  );
};

export default TaskSections;