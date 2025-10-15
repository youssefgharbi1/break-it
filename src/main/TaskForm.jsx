import { useState } from 'react';
import {
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  TextField,
  Select,
  MenuItem,
  InputLabel,
  FormControl,
  Button,
  Grid,
  Typography
} from '@mui/material';
import { LocalizationProvider, DateTimePicker } from '@mui/x-date-pickers';
import { AdapterDateFns } from '@mui/x-date-pickers/AdapterDateFns';
import styles from './TaskForm.module.css'

const TaskForm = ({ familyMembers, onSubmit, onCancel, open }) => {
  const [taskData, setTaskData] = useState({
    title: '',
    description: '',
    status: 'pending',
    priority: 'medium',
    category: '',
    assigned_to: null,
    start_time: null,
    due_time: null,
    estimated_duration: null,
    recurring_pattern: '',
    points_value: 0,
    room_id: null
  });

  const handleChange = (e) => {
    const { name, value } = e.target;
    setTaskData(prev => ({ ...prev, [name]: value }));
  };

  const handleDateTimeChange = (name, value) => {
    setTaskData(prev => ({ ...prev, [name]: value }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    onSubmit({
      ...taskData,
      start_time: taskData.start_time?.toISOString(),
      due_time: taskData.due_time?.toISOString()
    });
  };

  const categories = ['Household', 'Work', 'Personal', 'Family'];
  const statusOptions = ['pending', 'in_progress', 'completed', 'approved', 'rejected'];
  const priorityOptions = ['low', 'medium', 'high'];
  const recurringOptions = ['', 'daily', 'weekly', 'monthly'];


return (
  <Dialog open={open} onClose={onCancel} maxWidth="md" fullWidth className={styles.dialogContainer}>
    <DialogTitle className={styles.dialogTitle}>
      <Typography variant="h5" component="div">
        Assign New Task
      </Typography>
    </DialogTitle>
    
    <form onSubmit={handleSubmit}>
      <DialogContent dividers className={styles.dialogContent}>
        <Grid container spacing={2} className={styles.formGrid}>
          <Grid item xs={12}>
            <TextField
              fullWidth
              label="Title "
              name="title"
              value={taskData.title}
              onChange={handleChange}
              required
              margin="normal"
              className={styles.inputField}
              InputProps={{
                classes: {
                  root: styles.inputField,
                }
              }}
            />
          </Grid>
          
          <Grid item xs={12}>
            <TextField
              fullWidth
              label="Description"
              name="description"
              value={taskData.description}
              onChange={handleChange}
              multiline
              rows={3}
              margin="normal"
              className={styles.textareaField}
            />
          </Grid>
          
          <Grid item xs={6}>
            <FormControl fullWidth margin="normal" className={styles.formControl}>
              <InputLabel className={styles.formLabel}>Status</InputLabel>
              <Select
                name="status"
                value={taskData.status}
                onChange={handleChange}
                label="Status"
                className={styles.selectField}
              >
                {statusOptions.map(status => (
                  <MenuItem key={status} value={status}>
                    {status.replace('_', ' ')}
                  </MenuItem>
                ))}
              </Select>
            </FormControl>
          </Grid>
          
          <Grid item xs={6}>
            <FormControl fullWidth margin="normal" className={styles.formControl}>
              <InputLabel className={styles.formLabel}>Priority</InputLabel>
              <Select
                name="priority"
                value={taskData.priority}
                onChange={handleChange}
                label="Priority"
                className={styles.selectField}
              >
                {priorityOptions.map(priority => (
                  <MenuItem key={priority} value={priority}>
                    {priority}
                  </MenuItem>
                ))}
              </Select>
            </FormControl>
          </Grid>
          
          <Grid item xs={12}>
            <FormControl fullWidth margin="normal" className={styles.formControl}>
              <InputLabel className={styles.formLabel}>Category</InputLabel>
              <Select
                name="category"
                value={taskData.category}
                onChange={handleChange}
                label="Category"
                className={styles.selectField}
              >
                {categories.map(category => (
                  <MenuItem key={category} value={category}>
                    {category}
                  </MenuItem>
                ))}
              </Select>
            </FormControl>
          </Grid>
          
          <Grid item xs={6}>
            <FormControl fullWidth margin="normal" className={styles.formControl}>
              <InputLabel className={styles.formLabel}>Assigned To</InputLabel>
              <Select
                name="assigned_to"
                value={taskData.assigned_to || ''}
                onChange={handleChange}
                label="Assigned To"
                className={styles.selectField}
              >
                <MenuItem value="">Select member</MenuItem>
                {familyMembers.map(member => (
                  <MenuItem key={member.id} value={member.id}>
                    {member.name}
                  </MenuItem>
                ))}
              </Select>
            </FormControl>
          </Grid>
          
          <Grid item xs={6}>
            <TextField
              fullWidth
              label="Points Value"
              name="points_value"
              type="number"
              value={taskData.points_value}
              onChange={handleChange}
              margin="normal"
              inputProps={{ min: 0 }}
              className={styles.inputField}
            />
          </Grid>
          
          <Grid item xs={6}>
            <FormControl fullWidth margin="normal" className={styles.formControl}>
              <LocalizationProvider dateAdapter={AdapterDateFns}>
                <DateTimePicker
                  label="Start Time"
                  value={taskData.start_time}
                  onChange={(value) => handleDateTimeChange('start_time', value)}
                  renderInput={(params) => (
                    <TextField 
                      {...params} 
                      fullWidth 
                      margin="normal" 
                      className={styles.dateTimePicker}
                    />
                  )}
                />
              </LocalizationProvider>
            </FormControl>
          </Grid>
          
          <Grid item xs={6}>
            <FormControl fullWidth margin="normal" className={styles.formControl}>
              <LocalizationProvider dateAdapter={AdapterDateFns}>
                <DateTimePicker
                  label="Due Time"
                  value={taskData.due_time}
                  onChange={(value) => handleDateTimeChange('due_time', value)}
                  renderInput={(params) => (
                    <TextField 
                      {...params} 
                      fullWidth 
                      margin="normal" 
                      className={styles.dateTimePicker}
                    />
                  )}
                />
              </LocalizationProvider>
            </FormControl>
          </Grid>
          
          <Grid item xs={12}>
            <TextField
              fullWidth
              label="Estimated Duration (minutes)"
              name="estimated_duration"
              type="number"
              value={taskData.estimated_duration || ''}
              onChange={handleChange}
              margin="normal"
              inputProps={{ min: 1 }}
              className={styles.inputField}
            />
          </Grid>
          
          <Grid item xs={12}>
            <FormControl fullWidth margin="normal" className={styles.formControl}>
              <InputLabel className={styles.formLabel}>Recurring Pattern</InputLabel>
              <Select
                name="recurring_pattern"
                value={taskData.recurring_pattern}
                onChange={handleChange}
                label="Recurring Pattern"
                className={styles.selectField}
              >
                {recurringOptions.map(pattern => (
                  <MenuItem key={pattern || 'none'} value={pattern}>
                    {pattern || 'None'}
                  </MenuItem>
                ))}
              </Select>
            </FormControl>
          </Grid>

          <Grid item xs={12}>
            <TextField
              fullWidth
              label="Completion Notes"
              name="completion_notes"
              value={taskData.completion_notes || ''}
              onChange={handleChange}
              multiline
              rows={2}
              margin="normal"
              className={styles.textareaField}
            />
          </Grid>
        </Grid>
      </DialogContent>
      
      <DialogActions className={styles.dialogActions}>
        <Button 
          onClick={onCancel} 
          className={styles.cancelButton}
        >
          Cancel
        </Button>
        <Button 
          type="submit" 
          variant="contained" 
          className={styles.submitButton}
        >
          Assign Task
        </Button>
      </DialogActions>
    </form>
  </Dialog>
);

}
export default TaskForm;