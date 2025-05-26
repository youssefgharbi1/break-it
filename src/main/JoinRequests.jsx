import { useState, useEffect, useRef } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import styles from './JoinRequests.module.css';

const JoinRequests = ({ roomId }) => {
  const [requests, setRequests] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState('');
  const [isVisible, setIsVisible] = useState(false);
  const [isManuallyHidden, setIsManuallyHidden] = useState(true);
  const autoHideTimerRef = useRef(null);

  // Reset the 6-second auto-hide timer
  const resetAutoHideTimer = () => {
    clearTimeout(autoHideTimerRef.current);
    if (requests.length > 0 && !isManuallyHidden) {
      autoHideTimerRef.current = setTimeout(() => {
        setIsVisible(false);
      }, 6000);
    }
  };
  const fetchJoinRequests = async () => {
    try {
      const response = await fetch(
        `http://localhost/break-it-api/public/RoomMembers/?room_id=${roomId}&action=request`,
        { credentials: 'include', headers: { 'Accept': 'application/json' } }
      );

      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
      
      const data = await response.json();
      console.log(data)
      console.log(requests)
      setRequests(prev => {
          const prevRequests = prev.map(r => r.member_id);
          const newRequests = data.data.map(r => r.member_id);
          console.log(newRequests + " " + prevRequests)
          const same = prevRequests.length === newRequests.length &&
                       prevRequests.every((id, idx) => id === newRequests[idx]);
          if (same) return prev; // No update if identical
          return data.data;});
      
      // Only make visible if there are requests and not manually hidden
      if (data.data?.length > 0 && !isManuallyHidden) {
        setIsVisible(true);
        resetAutoHideTimer();
      }
    } catch (err) {
      setError(err.message);
    } finally {
      setIsLoading(false);
    }
  };

  const handleRequestResponse = async (action, id) => {
    try {
      const response = await fetch(
        `http://localhost/break-it-api/public/RoomMembers/`,
        {
          method: 'PATCH',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action, member_id: id, room_id: roomId })
        }
      );

      if (!response.ok) throw new Error(`Failed to ${action} request`);
      
      setRequests(prev => prev.filter(r => r.member_id !== id));
      resetAutoHideTimer();
    } catch (err) {
      setError(err.message);
    }
  };

  const toggleVisibility = () => {
    if (isManuallyHidden) {
      setIsManuallyHidden(false);
      if (requests.length > 0) {
        setIsVisible(true);
        resetAutoHideTimer();
      }
    } else {
      setIsManuallyHidden(true);
      setIsVisible(false);
      clearTimeout(autoHideTimerRef.current);
    }
  };

  useEffect(() => {
    fetchJoinRequests();
    const interval = setInterval(fetchJoinRequests, 10000);
    return () => {
      clearInterval(interval);
      clearTimeout(autoHideTimerRef.current);
    };
  }, [roomId]);

  useEffect(() => {
    if (!isManuallyHidden) {
      setIsVisible(requests.length > 0);
      resetAutoHideTimer();
    }
  }, [requests, isManuallyHidden]);

  if (isLoading) return null;
  if (error) return <div className={styles.error}>{error}</div>;

  return (
    <>
      {/* Toggle Button (always visible) */}
      <button 
        onClick={toggleVisibility}
        className={styles.toggleButton}
      >
        {isManuallyHidden ? 'Show Requests' : 'Hide Requests'}
      </button>

      {/* Requests Overlay */}
      <AnimatePresence>
        {isVisible && (
          <motion.div
            className={styles.requestsOverlay}
            initial={{ opacity: 0, y: -10 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -10 }}
            transition={{ duration: 0.3 }}
            onHoverStart={() => clearTimeout(autoHideTimerRef.current)}
            onHoverEnd={resetAutoHideTimer}
          >
            <div className={styles.requestsContainer}>
              <div className={styles.headerRow}>
                <h3 className={styles.requestsTitle}>Join Requests</h3>
                <button 
                  onClick={toggleVisibility}
                  className={styles.closeButton}
                >
                  ×
                </button>
              </div>
              
              <ul className={styles.requestsList}>
                <AnimatePresence>
                  {requests.map((request) => (
                    <motion.li
                      key={request.member_id}
                      className={styles.requestItem}
                      initial={{ opacity: 0, height: 0 }}
                      animate={{ opacity: 1, height: 'auto' }}
                      exit={{ opacity: 0, height: 0 }}
                      transition={{ duration: 0.2 }}
                    >
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
                    </motion.li>
                  ))}
                </AnimatePresence>
              </ul>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </>
  );
};

export default JoinRequests;