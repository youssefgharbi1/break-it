import React, { Component } from 'react';

class ErrorBoundary extends Component {
  constructor(props) {
    super(props);
    this.state = {
      hasError: false,
      error: null,
      errorInfo: null,
    };
  }

  static getDerivedStateFromError(error) {
    // Update state to indicate an error has occurred
    return { hasError: true };
  }

  componentDidCatch(error, errorInfo) {
    // Log the error and error info for debugging
    console.log('Error caught by ErrorBoundary:', error);
    console.log('Error information:', errorInfo);
    this.setState({ error, errorInfo });
  }

  render() {
    if (this.state.hasError) {
      return (
        <div>
          <h1>Something went wrong.</h1>
          <details style={{ whiteSpace: 'pre-wrap' }}>
            {this.state.error && this.state.error.toString()}
            <br />
            {/* Safely check if errorInfo is defined before accessing componentStack */}
            {this.state.errorInfo ? this.state.errorInfo.componentStack : 'No stack trace available'}
          </details>
        </div>
      );
    }

    // Otherwise, render the children components normally
    return this.props.children;
  }
}

export default ErrorBoundary;
