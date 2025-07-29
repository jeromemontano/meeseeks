import './styles/app.css';

// Stimulus setup
import { startStimulusApp } from '@symfony/stimulus-bridge';
const app = startStimulusApp(require.context('./controllers', true, /\.js$/));
