import { useNavigate } from 'react-router-dom';
import { useState, useEffect, useContext } from 'react';
import styles from './Dashboard.module.css';
import SessionContext from '../session/SessionContext';

const Dashboard = () => {
  const navigate = useNavigate();
  const { user } = useContext(SessionContext);
  const [roomCode, setRoomCode] = useState('');
  const [userRooms, setUserRooms] = useState([]);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  
  // Fetch user's rooms
  const fetchUserRooms = async () => {
    setIsLoading(true);
    setError('');
    
    try {
      const response = await fetch(`http://localhost/break-it-api/public/room/`, {
        credentials: 'include',
        headers: { 'Accept': 'application/json' }
      });


      if (!response.ok) {
        const errorText = await response.text();
        throw new Error(`Server error: ${response.status} - ${errorText}`);
      }

      const result = await response.json();
      
      if (!result.success || !result.data) {
        throw new Error(result.message || 'Invalid response format');
      }

      const rooms = result.data.map(room => ({
        id: room.room_id,
        name: room.room_name,
        status: room.request_status,
        joinedAt: room.joined_at,
        code: room.room_code
      }));

      setUserRooms(rooms);
      
    } catch (error) {
      console.error('Error fetching rooms:', error);
      setError(error.message || 'Failed to load rooms. Please try again.');
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    fetchUserRooms();
  }, []);

  const handleLogout = async () => {
    try {
      const response = await fetch('http://localhost/break-it-api/public/authentification/logout.php', {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' }
      });
  
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
      
      const data = await response.json();
      if (!data.success) throw new Error(data.message);
      
      navigate('/');
    } catch (error) {
      console.error('Logout error:', error);
      setError('Logout failed. Please try again.');
    }
  };

  const handleJoinRoom = async (e) => {
    e.preventDefault();
    setError('');
    setSuccess('');
    setIsLoading(true);

    try {
      const response = await fetch('http://localhost/break-it-api/public/roomMembers/', {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
          room_code: roomCode.toUpperCase() ,
          member_id: user.id
        })
      });
  
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
      
      const data = await response.json();
      if (!data.success) throw new Error(data.error || 'Failed to join room');
      
      setSuccess(data.message || 'Successfully joined the room!');
      setRoomCode('');
      fetchUserRooms(); // Refresh rooms list
    } catch (error) {
      console.error('Join room error:', error);
      setError(error.message || 'Failed to join room. Please check the code and try again.');
    } finally {
      setIsLoading(false);
    }
  };

  const handleNavigateToCreateRoom = () => {
    navigate('/create-room');
  };

  const handleEnterRoom = (roomId) => {
    navigate(`/room/${roomId}`);
  };
  
  
  return (
    <div className={styles.dashboard}>
      <h1>Home</h1>
      
      {/* Join Room Section */}
      <div className={styles.section}>
        <h2>Enter Code to Join Room</h2>
        <form onSubmit={handleJoinRoom} className={styles.joinForm}>
          <div className={styles.inputGroup}>
            <input
              type="text"
              placeholder="Enter 6-digit code"
              className={styles.codeInput}
              value={roomCode}
              onChange={(e) => {
                const value = e.target.value.slice(0, 6);
                setRoomCode(value);
              }}
              pattern="[A-Za-z0-9]{6}"
              title="Please enter a 6-character alphanumeric code"
              required
            />
            <button 
              type="submit" 
              className={styles.joinButton}
              disabled={roomCode.length < 6 || isLoading}
            >
              {isLoading ? (
                <span className={styles.spinner}></span>
              ) : (
                'Join Room'
              )}
            </button>
          </div>
          {roomCode.length > 0 && roomCode.length < 6 && (
            <p className={styles.codeHint}>Code must be 6 characters</p>
          )}
        </form>
      </div>
      
      {/* Create Room Section */}
      {user.role === 'P' ? (
      <div className={styles.section}>
        <h2>Create New Room</h2>
        <button 
          onClick={handleNavigateToCreateRoom}
          className={styles.createButton}
        >
          Create New Room
        </button>
      </div>
      ) : null}
      
      {/* Your Rooms Section */}
      <div className={styles.section}>
        <h2>Your Rooms</h2>
        {isLoading ? (
          <p className={styles.loading}>Loading rooms...</p>
        ) : userRooms.length === 0 ? (
          <p className={styles.emptyState}>You're not a member of any rooms yet.</p>
        ) : (
          <ul className={styles.roomList}>
            {userRooms.map(room => (
              <li key={room.id} className={styles.roomItem}>
                <div className={styles.roomInfo}>
                  <h3>{room.name}</h3>
                  {room.code && <p className={styles.roomCode}>Code: {room.code}</p>}
                </div>
                <button 
                  onClick={() => handleEnterRoom(room.id)}
                  className={styles.enterButton}
                >
                  Enter
                </button>
              </li>
            ))}
          </ul>
        )}
      </div>
      
      {/* Status Messages */}
      {error && <div className={styles.error}>{error}</div>}
      {success && <div className={styles.success}>{success}</div>}
      
      {/* Logout Button */}
      <button 
        onClick={handleLogout}
        className={styles.logoutButton}
      >
        Log Out
      </button>
    </div>
  );
};

export default Dashboard;