import React, {useState} from "react";
import '../assets/style/login.css';

import logoSvg from '../assets/img/logo.svg';
import mailSvg from '../assets/img/mail.svg';
import passSvg from '../assets/img/pass.svg';
import trainSvg from '../assets/img/train/train.svg';



interface FormMessage{
    text: string,
    color: string;
}

export default function Login(){
    //STATE FOR INPUTS AND UI
    const[email, setEmail] = useState<string>('');
    const[pass, setPass] = useState<string>('');
    const[message, setMessage] = useState<FormMessage>({text: '', color: ''});
    const[isLoading, setIsLoading] = useState<boolean>(false);

    //HANDLE LOGIN EVENT
    const handleLogin = async(e: React.MouseEvent<HTMLButtonElement>) => {
        e.preventDefault();

        //reset message
        setMessage({text: '', color: ''});

        //validation
        if (!email || !pass){
            setMessage({ text: 'Veuillez remplir tous les champs.', color: '#e05252' });
            return;
        }

        setIsLoading(true);

        try{
            //sending request to API
            const response = await fetch('http://localhost:8000/api_login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ mail: email, pass: pass })
            });
            const data = await response.json();


            //redirect to homepage after success
            if(data.status === 'success'){
                setMessage({text: 'Connextion reussie!', color: '#2d9e6b'});
                setTimeout(() => {
                    window.location.href = '/'; // React Router path for home
                }, 1500);
            }else{
                setMessage({text: data.message, color: "#e05252"});
                setIsLoading(false);
            }
        } catch(error){
            console.error('Login error: ', error);
            setMessage({text: "Error de connexion au server", color: "#e05252"});
            setIsLoading(false);
        }
    };

    return (
        <>
        <div className="login-page">
            <div className="left-panel">
                <div className="left-content">
                    <div className="brand">
                    <div className="brand-logo">
                        <a href="/"><img src={logoSvg} alt="" /></a>
                    </div>
                    </div>

                    <div className="hero-text">
                    <h1>Voyagez<br />à grande <em>vitesse</em></h1>
                    <p>Réservez vos billets TGV en quelques clics. Confort, rapidité et sérénité pour tous vos déplacements.</p>
                    <div className="stats">
                        <div className="stat-item">
                        <div className="stat-num-login">320</div>
                        <div className="stat-label">km/h max</div>
                        </div>
                        <div className="stat-item">
                        <div className="stat-num-login">200+</div>
                        <div className="stat-label">destinations</div>
                        </div>
                        <div className="stat-item">
                        <div className="stat-num-login">99%</div>
                        <div className="stat-label">ponctualité</div>
                        </div>
                    </div>
                    </div>
                </div>

                {/* <!-- Animated tracks --> */}
                <div className="tracks">
                    <div className="track-line"></div>
                    <div className="track-line"></div>
                    <div className="sleepers">
                    {/* <!-- generate many sleepers via JS below --> */}
                    {Array.from({length: 40}).map((_, i) =>(
                        <div key={i} className="sleeper"></div>
                    ))}
                    </div>
                    <div className="train-silhouette">
                    <div className="hero-train">
                        <img src={trainSvg} alt="TGV Train" style={{height: "250px", width: "auto"}} />
                    </div>
                    </div>
                </div>
            </div>

            <div className="right-panel">
                    <div className="login-card">
                        <h2 className="login-title">Bon retour</h2>
                        <p className="login-subtitle">Pas encore de compte ? <a href="/signup">S'inscrire</a></p>

                        <div className="form-group">
                        <label className="form-label">Adresse e-mail</label>
                        <div className="input-wrap">
                            <span className="input-icon">
                                <img src={mailSvg} alt="" />
                            </span>
                            <input className="form-input" type="email"  id="email" placeholder="votre@email.com" value={email} onChange={(e) => setEmail(e.target.value)} />
                        </div>
                        </div>

                        <div className="form-group">
                        <label className="form-label">Mot de passe</label>
                        <div className="input-wrap">
                            <span className="input-icon">
                                <img src={passSvg} alt="" />
                            </span>
                            <input className="form-input" type="password" id="password" placeholder="••••••••" value={pass} onChange={(e) => setPass(e.target.value)} />
                        </div>
                        {/* WE NEED DO PAGE "Mot de passe oublie" */}
                        <a href="#" className="forgot-link">Mot de passe oublié ?</a>
                        </div>

                        {message.text && (
                            <div id="login-message" style={{ marginBottom: '15px', fontSize: '14px', display: 'block', color: message.color }}>
                            {message.text}
                            </div>
                        )}

                        <button className="btn-login" id="btn-login" onClick={handleLogin} disabled={isLoading}>{isLoading ? 'CHARGEMENT...' : "SE CONNECTER"}</button>

                        <div className="divider">
                        <div className="divider-line"></div>
                        <span>ou</span>
                        <div className="divider-line"></div>
                        </div>

                        <p className="signup-link">Vous n'avez pas de compte ? <a href="./signup">Créer un compte</a></p>
                    </div>
            </div>
        </div>
        </>
    );
} 


