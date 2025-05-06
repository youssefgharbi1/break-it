import { useState, useEffect, useRef, useContext } from 'react';
import styles from './Messages.module.css';
import SessionContext from '../session/SessionContext';

const Messages = ({ roomId }) => {
  const { user } = useContext(SessionContext);
  const [messages, setMessages] = useState({ messages: [] });
  const [newMessage, setNewMessage] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState('');
  const messagesEndRef = useRef(null);
  const fetchIntervalRef = useRef();

  const fetchMessages = async () => {
    try {
      const response = await fetch(
        `http://localhost/break-it-api/public/messages/?room_id=${roomId}`,
        {
          credentials: 'include',
          headers: { 'Accept': 'application/json' }
        }
      );

      if (!response.ok) {
        throw new Error(`Error: ${response.status}`);
      }
      
      const data = await response.json();
      if (data.success) {
        setMessages(data);
      }
    } catch (err) {
      setError(err.message);
    }
  };

  const sendMessage = async () => {
    const trimmedMessage = newMessage.trim();
    if (!trimmedMessage || !user) return;
    
    try {
      setIsLoading(true);
      const response = await fetch(
        'http://localhost/break-it-api/public/messages/',
        {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            room_id: roomId,
            content: trimmedMessage
          })
        }
      );

      if (!response.ok) {
        throw new Error(`Error: ${response.status}`);
      }
      
      const data = await response.json();
      setMessages(prev => ({
        ...prev,
        messages: [...prev.messages, { message: data }]
      }));
      setNewMessage('');
    } catch (err) {
      setError(err.message);
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    fetchMessages();
    fetchIntervalRef.current = setInterval(fetchMessages, 5000);
    
    return () => {
      if (fetchIntervalRef.current) {
        clearInterval(fetchIntervalRef.current);
      }
    };
  }, [roomId]);

  useEffect(() => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  }, [messages]);

  const handleKeyPress = (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      sendMessage();
    }
  };

  if (!messages.messages) {
    return <div className={styles.loading}>Loading messages...</div>;
  }
  return (
    <div className={styles.messagesContainer}>
      {/* Scrollable messages area with fixed height */}
      <div className={styles.messagesList}>
        {messages.messages.map(({ message }) => (
          <div 
            key={message.id} 
            className={`${styles.message} ${
              message.user_id === user?.id ? styles.sent : styles.received
            }`}
          >
            <div className={styles.messageHeader}>
              <span className={styles.sender}>
                {message.user_id === user?.id ? 'You' : message.username}
              </span>
              <span className={styles.timestamp}>
                {new Date(message.created_at).toLocaleTimeString([], { 
                  hour: '2-digit', 
                  minute: '2-digit' 
                })}
              </span>
            </div>
            <div className={styles.messageContent}>
              {message.content}
            </div>
          </div>
        ))}
        {/* This empty div will be our scroll target */}
        <div ref={messagesEndRef} />
      </div>
  
      {/* Fixed input container at bottom */}
      <div className={styles.messageInputContainer}>
        {error && <div className={styles.error}>{error}</div>}
        <textarea
          value={newMessage}
          onChange={(e) => setNewMessage(e.target.value)}
          onKeyDown={handleKeyPress}
          placeholder="Type your message here..."
          disabled={isLoading}
          className={styles.messageInput}
          rows={3}
        />
        <button
          onClick={sendMessage}
          disabled={!newMessage.trim() || isLoading}
          className={styles.sendButton}
          aria-label="Send message"
        >
          {isLoading ? (
            <span className={styles.spinner} aria-hidden="true" />
          ) : (
            'Send'
          )}
        </button>
      </div>
    </div>
  );
}
export default Messages;