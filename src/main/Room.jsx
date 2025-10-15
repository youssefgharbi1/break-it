import { useState, useEffect, useContext, useMemo } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import SessionContext from '../session/SessionContext';
import Messages from './messages';
import JoinRequests from './JoinRequests';
import TaskForm from './TaskForm';
import RoomHeader from './RoomHeader';
import DateControls from './DateControls';
import TaskSections from './TaskSections';
import styles from './Room.module.css';

const Room = () => {
  const { roomId } = useParams();
  const navigate = useNavigate();
  const { user } = useContext(SessionContext);
  const [openTaskForm, setOpenTaskForm] = useState(false);
  const [room, setRoom] = useState(null);
  const [tasks, setTasks] = useState([]);
  const [familyMembers, setFamilyMembers] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState('');
  const [selectedDate, setSelectedDate] = useState(new Date());
  const apiUrl = import.meta.env.VITE_API_URL;

  // Fetch room data and tasks
  useEffect(() => {
    const fetchRoomData = async () => {
      try {
        setIsLoading(true);
        setError('');

        const membershipRes = await fetch(
          `${apiUrl}/break-it-api/public/Room/?id=${roomId}`,
          { credentials: 'include', headers: { 'Content-Type': 'application/json' } }
        );
        
        const membershipData = await membershipRes.json();
        if (!membershipData.success) throw new Error('You are not a member of this room');

        const [roomRes, tasksRes] = await Promise.all([
          fetch(`${apiUrl}/break-it-api/public/Room/?id=${roomId}`, {
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' },
            method: 'GET'
          }),
          fetch(`${apiUrl}/break-it-api/public/Task/?room_id=${roomId}`, {
            credentials: 'include'
          })
        ]);

        if (!roomRes.ok || !tasksRes.ok) throw new Error('Failed to fetch data');

        const [roomData, tasksData] = await Promise.all([roomRes.json(), tasksRes.json()]);
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
  const toBase64 = (file) => {
    return new Promise((resolve, reject) => {
      const reader = new FileReader();
      reader.readAsDataURL(file);
      reader.onload = () => resolve(reader.result);
      reader.onerror = reject;
    });
  };

  const handleRoomImageChange = async (file) => {
    try {
      const base64Image = await toBase64(file);

      const response = await fetch(`${apiUrl}/break-it-api/public/Room/?id=${roomId}`, {
        method: 'PUT',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          image: base64Image
        })
      });

      if (!response.ok) throw new Error('Failed to update room image');

      const updatedRoom = await response.json();
      setRoom(prev => ({ ...prev, image: updatedRoom.data.image }));
    } catch (err) {
      console.error('Image update error:', err);
      setError(err.message);
    }
  };
  

  // Task CRUD operations
  const handleTaskCreate = async (taskData) => {
    try {
      const response = await fetch(`${apiUrl}/break-it-api/public/Task/`, {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          ...taskData,
          created_by: user.id,
          room_id: parseInt(roomId)
        })
      });

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || 'Failed to create task');
      }

      const createdTask = await response.json();
      setTasks([...tasks, createdTask.data]);
      setOpenTaskForm(false);
    } catch (err) {
      console.error('Task creation error:', err);
      setError(err.message);
    }
  };

  const handleTaskUpdate = async (taskId, updatedFields) => {
    try {
      const response = await fetch(`${apiUrl}/break-it-api/public/Task/${taskId}`, {
        method: 'PUT',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(updatedFields)
      });

      if (!response.ok) throw new Error('Failed to update task');
      const updatedTask = await response.json();
      setTasks(tasks.map(task => task.id === taskId ? updatedTask.data : task));
    } catch (err) {
      console.error('Task update error:', err);
      setError(err.message);
    }
  };

  const handleTaskDelete = async (taskId) => {
    try {
      const response = await fetch(`${apiUrl}/break-it-api/public/Task/${taskId}`, {
        method: 'DELETE',
        credentials: 'include'
      });

      if (!response.ok) throw new Error('Failed to delete task');
      setTasks(tasks.filter(task => task.id !== taskId));
    } catch (err) {
      console.error('Task deletion error:', err);
      setError(err.message);
    }
  };

  // Memoized filtered tasks
  const { myAssignedTasks, tasksAssignedToMe } = useMemo(() => {
    const myAssigned = tasks.filter(task => task.createdBy === user.id);
    const assignedToMe = tasks.filter(task => task.assignedTo === user.id);
    return { myAssignedTasks: myAssigned, tasksAssignedToMe: assignedToMe };
  }, [tasks, user.id]);

  // Date filtering
  const filterByDate = useMemo(() => (taskList) => {
    if (!selectedDate) return taskList;
    const selectedDateString = selectedDate.toISOString().split('T')[0];
    return taskList.filter(task => {
      if (!task.dueTime) return false;
      const taskDatePart = task.dueTime.split(' ')[0];
      return taskDatePart === selectedDateString;
    });
  }, [selectedDate]);

  const filteredMyAssignedTasks = filterByDate(myAssignedTasks);
  const filteredTasksAssignedToMe = filterByDate(tasksAssignedToMe);

  if (isLoading) return <div className={styles.loading}>Loading room...</div>;
  if (error) return <div className={styles.error}>{error}</div>;
  if (!room) return <div className={styles.error}>Room not found</div>;


  return (
    <div className={styles.pageLayout}>
      {user.role === 'P' && <JoinRequests roomId={roomId} />}
      
      <div className={[styles.roomContainer, styles.leftContent].join(' ')}>
        <RoomHeader 
          room={room} 
          user={user} 
          onReturnHome={() => navigate('/dashboard')}
          onCreateTaskClick={() => setOpenTaskForm(!openTaskForm)}
          openTaskForm={openTaskForm}
          onImageChange={handleRoomImageChange}
        />

        
        {openTaskForm && (
          <TaskForm 
            open={openTaskForm}
            familyMembers={familyMembers}
            onSubmit={handleTaskCreate}
            onCancel={() => setOpenTaskForm(false)}
          />
        )}
        
        {error && <div className={styles.errorMessage}>{error}</div>}

        <DateControls 
          selectedDate={selectedDate} 
          onDateChange={setSelectedDate} 
        />

        <TaskSections 
          user={user}
          myAssignedTasks={filteredMyAssignedTasks}
          tasksAssignedToMe={filteredTasksAssignedToMe}
          onTaskUpdate={handleTaskUpdate}
          onTaskDelete={handleTaskDelete}
          selectedDate={selectedDate}
        />
      </div>
      <Messages roomId={roomId} />

    </div>
  );
};

export default Room;