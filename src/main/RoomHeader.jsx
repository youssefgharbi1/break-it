import React, { useState } from 'react';
import styles from './Room.module.css';
import { Avatar, Dialog, DialogTitle, DialogActions, Button } from '@mui/material';
import ImageIcon from '@mui/icons-material/Image'; // fallback icon


const RoomHeader = ({ room, user, onReturnHome, onCreateTaskClick, openTaskForm, onImageChange }) => {
  const [openDialog, setOpenDialog] = useState(false);
  const [selectedFile, setSelectedFile] = useState(null);
  const apiUrl = import.meta.env.VITE_API_URL;

  const handleImageClick = () => {
    if (user.role === 'P') {
      setOpenDialog(true);
    }
  };

  const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      setSelectedFile(file);
    }
  };

  const handleSaveImage = () => {
    if (selectedFile && onImageChange) {
      onImageChange(selectedFile); // Callback to parent to handle upload
    }
    setOpenDialog(false);
    setSelectedFile(null);
  };

  return (
    <header className={styles.roomHeader}>
      <div className={styles.headerLeft}>
        <button 
          onClick={onReturnHome}
          className={styles.homeButton}
          aria-label="Return to dashboard"
        >
          ← Home
        </button>
        <h1>{room.name}</h1>
        {room.image ? (
          <Avatar 
            src={`${apiUrl}/break-it-api/public${room.image}`} 
            alt="Room"
            onClick={handleImageClick}
            sx={{ width: 56, height: 56, marginLeft: 2, cursor: user.role === 'P' ? 'pointer' : 'default' }}
          />
        ) : (
          <Avatar 
            onClick={handleImageClick}
            sx={{ width: 56, height: 56, marginLeft: 2, cursor: user.role === 'P' ? 'pointer' : 'default', bgcolor: 'grey.300' }}
          >
            <ImageIcon />
          </Avatar>
        )}

      </div>

      {user.role === 'P' && (
        <>
          <p className={styles.roomCode}>Room Code: {room.code}</p>
          <button 
            onClick={onCreateTaskClick}
            className={styles.createButton}
          >
            {openTaskForm ? 'Cancel' : 'Assign New Task'}
          </button>
        </>
      )}

      <Dialog 
        open={openDialog} 
        onClose={() => setOpenDialog(false)}
        PaperProps={{
          sx: {
            backgroundColor: 'var(--bg-light)',
            color: 'var(--text-dark)',
            borderRadius: '12px',
            padding: '1rem',
          }
        }}
      >
        <DialogTitle 
          sx={{ 
            color: 'var(--primary-dark)', 
            fontWeight: 'bold', 
            borderBottom: '1px solid var(--primary-light)' 
          }}
        >
          Change Room Image
        </DialogTitle>

        <input 
          type="file" 
          accept="image/*" 
          onChange={handleFileChange} 
          className="input-theme"
        />

        <DialogActions 
          sx={{ 
            justifyContent: 'space-between', 
            padding: '1rem' 
          }}
        >
          <Button 
            onClick={() => setOpenDialog(false)}
            sx={{
              backgroundColor: 'var(--secondary)',
              color: 'var(--text-dark)',
              '&:hover': {
                backgroundColor: 'var(--secondary-light)',
              }
            }}
          >
            Cancel
          </Button>
          <Button 
            onClick={handleSaveImage} 
            disabled={!selectedFile}
            sx={{
              backgroundColor: 'var(--primary)',
              color: 'var(--text-light)',
              '&:hover': {
                backgroundColor: 'var(--primary-dark)',
              },
              '&.Mui-disabled': {
                backgroundColor: 'var(--primary-light)',
                color: 'var(--text-medium)',
              }
            }}
          >
            Save
          </Button>
        </DialogActions>
      </Dialog>

    </header>
  );
};

export default RoomHeader;
