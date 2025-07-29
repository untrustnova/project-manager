// src/components/LoadingSpinner.jsx
import { RotateCw, Loader2, Clock, Circle } from 'lucide-react';

const LoadingSpinner = ({ 
  size = 'medium',
  variant = 'default',
  className = '',
  fullScreen = false
}) => {
  // Size options
  const sizeClasses = {
    small: 'w-5 h-5',
    medium: 'w-8 h-8',
    large: 'w-12 h-12',
    xlarge: 'w-16 h-16'
  };

  // Variant options
  const spinnerVariants = {
    default: (
      <RotateCw 
        className={`animate-spin ${sizeClasses[size]} ${className}`}
      />
    ),
    dots: (
      <div className={`flex space-x-1 ${className}`}>
        {[1, 2, 3].map((i) => (
          <div 
            key={i}
            className={`bg-current rounded-full animate-bounce`}
            style={{
              width: size === 'small' ? '6px' : 
                     size === 'medium' ? '8px' : 
                     size === 'large' ? '10px' : '12px',
              height: size === 'small' ? '6px' : 
                      size === 'medium' ? '8px' : 
                      size === 'large' ? '10px' : '12px',
              animationDelay: `${i * 0.15}s`
            }}
          />
        ))}
      </div>
    ),
    circle: (
      <div className={`relative ${sizeClasses[size]} ${className}`}>
        <Circle className="absolute opacity-30" />
        <Circle 
          className="absolute animate-spin origin-center" 
          style={{ strokeDasharray: 80, strokeDashoffset: 60 }}
        />
      </div>
    ),
    clock: (
      <Clock className={`animate-spin ${sizeClasses[size]} ${className}`} />
    ),
    bar: (
      <div className={`w-full ${className}`}>
        <div className="relative h-1 w-full overflow-hidden bg-gray-200 rounded-full">
          <div className="absolute h-full w-1/3 bg-blue-500 animate-loadingBar" />
        </div>
      </div>
    )
  };

  const spinner = spinnerVariants[variant] || spinnerVariants.default;

  if (fullScreen) {
    return (
      <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-30">
        <div className="p-6 bg-white rounded-lg shadow-xl">
          {spinner}
        </div>
      </div>
    );
  }

  return spinner;
};

// Add some global CSS for animations (put this in your main CSS file)
/*
@keyframes spin {
  to { transform: rotate(360deg); }
}
@keyframes bounce {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-5px); }
}
@keyframes loadingBar {
  0% { left: -33%; }
  100% { left: 100%; }
}
*/

export default LoadingSpinner;