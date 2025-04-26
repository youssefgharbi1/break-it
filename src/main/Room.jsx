import { useState, useEffect, useContext } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import Task from './Task';
import TaskForm from './TaskForm';
import styles from './Room.module.css';
import SessionContext from '../session/SessionContext';
import Messages from './messages';
import JoinRequests from './JoinRequests';

const Room = () => {
  const { roomId } = useParams();
  const navigate = useNavigate();
  const { user } = useContext(SessionContext);
  const [room, setRoom] = useState(null);
  const [tasks, setTasks] = useState([]);
  const [familyMembers, setFamilyMembers] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState('');
  const [showTaskForm, setShowTaskForm] = useState(false);

  // Fetch room data and tasks
  useEffect(() => {
    const fetchRoomData = async () => {
      try {
        setIsLoading(true);
        setError('');

        // Verify room membership
        const membershipRes = await fetch(
          `http://localhost/break-it-api/public/Room/?id=${roomId}`,
          {
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' }
          }
        );
        const membershipData = await membershipRes.json();
        
        if (!membershipData.success) {
          throw new Error('You are not a member of this room');
        }

        // Fetch room details and tasks in parallel
        const [roomRes, tasksRes] = await Promise.all([
          fetch(`http://localhost/break-it-api/public/Room/?id=${roomId}`, {
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' },
            method : 'GET'
          }),
          fetch(`http://localhost/break-it-api/public/Task/?room_id=${roomId}`, {
            credentials: 'include'
          })
        ]);

        if (!roomRes.ok) throw new Error('Failed to fetch room details');
        if (!tasksRes.ok) throw new Error('Failed to fetch tasks');

        const roomData = await roomRes.json();
        const tasksData = await tasksRes.json();
        setRoom(roomData.data);
        setTasks(tasksData.data || []);
        setFamilyMembers(roomData.data.roomMembers || []);
      } catch (err) {
        console.error('Room data fetch error:', err);
        setError(err.message);
        if (err.message.includes('not a member')) {
          navigate('/dashboard', { state: { error: err.message } });
        }
      } finally {
        setIsLoading(false);
      }
    };

    fetchRoomData();

  }, [roomId, navigate]);

  // Task CRUD operations
  const handleTaskCreate = async (taskData) => {
    try {
      const response = await fetch(
        'http://localhost/break-it-api/public/Task/',
        {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            ...taskData,
            created_by: user.id,
            room_id: parseInt(roomId)
          })
        }
      );

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || 'Failed to create task');
      }

      const createdTask = await response.json();
      setTasks([...tasks, createdTask.data]);
      setShowTaskForm(false);
    } catch (err) {
      console.error('Task creation error:', err);
      setError(err.message);
    }
  };

  const handleTaskUpdate = async (taskId, updatedFields) => {
    try {
      const response = await fetch(
        `http://localhost/break-it-api/public/Task/${taskId}`,
        {
          method: 'PUT',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(updatedFields)
        }
      );

      if (!response.ok) throw new Error('Failed to update task');
      
      const updatedTask = await response.json();
      setTasks(tasks.map(task => 
        task.id === taskId ? updatedTask.data : task
      ));
    } catch (err) {
      console.error('Task update error:', err);
      setError(err.message);
    }
  };

  const handleTaskDelete = async (taskId) => {
    try {
      const response = await fetch(
        `http://localhost/break-it-api/public/Task/${taskId}`,
        {
          method: 'DELETE',
          credentials: 'include'
        }
      );

      if (!response.ok) throw new Error('Failed to delete task');
      
      setTasks(tasks.filter(task => task.id !== taskId));
    } catch (err) {
      console.error('Task deletion error:', err);
      setError(err.message);
    }
  };
  const renderRequestSection = () => {
    if (user.role !== 'P') return null;
    return (
      <JoinRequests roomId={roomId}/>
    );

  }
  const renderCreateTaskSection = () => {
    if (user.role !== 'P') return null;
    return (
      <button 
          onClick={() => setShowTaskForm(!showTaskForm)}
          className={styles.createButton}
        >
          {showTaskForm ? 'Cancel' : 'Create New Task'}
        </button>
    );

  }
  const handleReturnHome = () => {
    navigate('/dashboard');
  };

  // Filter tasks by status for better organization
  const pendingTasks = tasks.filter(t => t.status === 'pending');
  const inProgressTasks = tasks.filter(t => t.status === 'in-progress');
  const completedTasks = tasks.filter(t => t.status === 'completed');

  if (isLoading) return <div className={styles.loading}>Loading room...</div>;
  if (error) return <div className={styles.error}>{error}</div>;
  if (!room) return <div className={styles.error}>Room not found</div>;

  return (
    <div className={styles.pageLayout}>
    {renderRequestSection}
    <div className={[styles.roomContainer, styles.leftContent].join(' ')}>
      <header className={styles.roomHeader}>
        <div className={styles.headerLeft}>
          <button 
            onClick={handleReturnHome}
            className={styles.homeButton}
            aria-label="Return to dashboard"
          >
            ← Home
          </button>
          <h1>{room.name}</h1>
        </div>
        <p className={styles.roomCode}>Room Code: {room.code}</p>
        {renderCreateTaskSection()}
      </header>
      
      {showTaskForm && (
        <TaskForm 
          familyMembers={familyMembers}
          onSubmit={handleTaskCreate}
          onCancel={() => setShowTaskForm(false)}
        />
        
      )}
      

      {error && <div className={styles.errorMessage}>{error}</div>}

      <div className={styles.taskSections}>
        {/* Pending Tasks */}
        <div className={styles.taskColumn}>
          <h2>Pending ({pendingTasks.length})</h2>
          {pendingTasks.length === 0 ? (
            <p className={styles.noTasks}>No pending tasks</p>
          ) : (
            pendingTasks.map(task => (
              <Task
                key={task.id}
                task={task}
                onUpdate={handleTaskUpdate}
                onDelete={handleTaskDelete}
              />
            ))
          )}
        </div>

        {/* In Progress Tasks */}
        <div className={styles.taskColumn}>
          <h2>In Progress ({inProgressTasks.length})</h2>
          {inProgressTasks.length === 0 ? (
            <p className={styles.noTasks}>No tasks in progress</p>
          ) : (
            inProgressTasks.map(task => (
              <Task
                key={task.id}
                task={task}
                onUpdate={handleTaskUpdate}
                onDelete={handleTaskDelete}
              />
            ))
          )}
        </div>

        {/* Completed Tasks */}
        <div className={styles.taskColumn}>
          <h2>Completed ({completedTasks.length})</h2>
          {completedTasks.length === 0 ? (
            <p className={styles.noTasks}>No completed tasks</p>
          ) : (
            completedTasks.map(task => (
              <Task
                key={task.id}
                task={task}
                onUpdate={handleTaskUpdate}
                onDelete={handleTaskDelete}
              />
            ))
          )}
        </div>
      </div>
    </div>
    <Messages roomId={roomId}/>
    </div>
  );
};

export default Room;