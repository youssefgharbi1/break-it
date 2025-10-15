import { useState, useContext } from 'react';
import { useNavigate } from 'react-router-dom';
import styles from './CreateRoom.module.css';
import SessionContext from '../session/SessionContext';

const CreateRoom = () => {
  const [roomData, setRoomData] = useState({
    name: '',
    description: ''
  });
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState('');
  const navigate = useNavigate();
  const apiUrl = import.meta.env.VITE_API_URL;
  
  // Get session data from context
  const { user } = useContext(SessionContext);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setRoomData(prev => ({ ...prev, [name]: value }));
  };

  const handleCreateRoom = async (e) => {
    e.preventDefault();
    setIsLoading(true);
    setError('');

    try {
      const response = await fetch(`${apiUrl}/break-it-api/public/room/`, {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
          name: roomData.name,
          description: roomData.description,
          family_id: user?.family_id // Use the user's family_id from context
        })
      });

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || 'Failed to create room');
      }

      const result = await response.json();
      navigate(`/room/${result.data.room_id}`);
    } catch (err) {
      console.error('Room creation error:', err);
      setError(err.message);
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className={styles.container}>
      <h1>Create New Room</h1>
      
      <form onSubmit={handleCreateRoom} className={styles.form}>
        <div className={styles.formGroup}>
          <label htmlFor="name">Room Name *</label>
          <input
            id="name"
            type="text"
            name="name"
            value={roomData.name}
            onChange={handleChange}
            required
            autoFocus
            maxLength={50}
            placeholder="Enter room name (max 50 characters)"
          />
        </div>
        
        <div className={styles.formGroup}>
          <label htmlFor="description">Description</label>
          <textarea
            id="description"
            name="description"
            value={roomData.description}
            onChange={handleChange}
            rows={4}
            maxLength={200}
            placeholder="Enter a brief description (max 200 characters)"
          />
          <p className={styles.charCount}>
            {roomData.description.length}/200 characters
          </p>
        </div>
        
        {error && <div className={styles.error}>{error}</div>}
        
        <div className={styles.buttonGroup}>
          <button 
            type="button" 
            onClick={() => navigate(-1)}
            className={styles.cancelButton}
            disabled={isLoading}
          >
            Cancel
          </button>
          <button 
            type="submit" 
            className={styles.createButton}
            disabled={isLoading || !roomData.name.trim()}
          >
            {isLoading ? (
              <>
                <span className={styles.spinner}></span>
                Creating...
              </>
            ) : 'Create Room'}
          </button>
        </div>
      </form>
    </div>
  );
};

export default CreateRoom;