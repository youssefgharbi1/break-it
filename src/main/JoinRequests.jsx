import { useState, useEffect } from 'react';
import styles from './JoinRequests.module.css';

const JoinRequests = ({ roomId }) => {
  const [requests, setRequests] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState('');

  // Fetch join requests for this room
  const fetchJoinRequests = async () => {
    try {
      const response = await fetch(
        `http://localhost/break-it-api/public/RoomMembers/?room_id=${roomId}&action=request`,
        {
          
          credentials: 'include',
          headers: { 'Accept': 'application/json' }
        }
      );

      if (!response.ok) {
        const errorData = await response.json().catch(() => ({}));
        throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
      }

      const data = await response.json();
      setRequests(data.data || []);
      
    } catch (err) {
      setError(err.message);
    } finally {
      setIsLoading(false);
    }
  };

  // Handle request response (approve/reject)
  const handleRequestResponse = async (action, id) => {
    try {
      const response = await fetch(
        `http://localhost/break-it-api/public/RoomMembers/`,
        {
          method: 'PATCH',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action : action, member_id : id, room_id : roomId })
        }
      );

      if (!response.ok) throw new Error(`Failed to ${action} request`);
      
      // Refresh the requests list
      fetchJoinRequests();
    } catch (err) {
      setError(err.message);
    }
  };

  useEffect(() => {
    fetchJoinRequests();
    const interval = setInterval(fetchJoinRequests, 10000); // Refresh every 10 seconds
    
    return () => clearInterval(interval);
  }, [roomId]);

  if (isLoading) return null; // Or loading spinner
  if (error) return <div className={styles.error}>{error}</div>;
  if (requests.length === 0) return null;

  return (
    <div className={styles.requestsOverlay}>
      <div className={styles.requestsContainer}>
        <h3 className={styles.requestsTitle}>Join Requests</h3>
        
        <ul className={styles.requestsList}>
          {requests.map(request => (
            <li key={request.member_id} className={styles.requestItem}>
              <div className={styles.requestInfo}>
                <span className={styles.requestName}>{request.username}</span>
                <span className={styles.requestEmail}>{request.email}</span>
              </div>
              
              <div className={styles.requestActions}>
                <button
                  onClick={() => handleRequestResponse('approve', request.member_id)}
                  className={styles.approveButton}
                >
                  Approve
                </button>
                <button
                  onClick={() => handleRequestResponse('reject', request.member_id)}
                  className={styles.rejectButton}
                >
                  Reject
                </button>
              </div>
            </li>
          ))}
        </ul>
      </div>
    </div>
  );
};

export default JoinRequests;