import { useNavigate } from 'react-router-dom';
import styles from './Dashboard.module.css';

const Dashboard = () => {
  const navigate = useNavigate();

  const handleLogout = async () => {
    try {
      const response = await fetch('http://localhost/break-it-api/public/logout.php', {
        method: 'POST',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json'
        }
      });
  
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
      
      const data = await response.json();
      if (!data.success) throw new Error(data.message);
      
      console.log('Logout successful');
      navigate('/')
      
    } catch (error) {
      console.error('Logout error:', error);
      // Show user-friendly error
    }
  };

  return (
    <div className={styles.dashboard}>
      <h1>Welcome to Your Dashboard</h1>
      <p>You are successfully logged in!</p>
      
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