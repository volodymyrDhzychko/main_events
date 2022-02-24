import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import CompulsoryFields from './CompulsoryFields.jsx';
import AdditionalFields from './AdditionalFields.jsx';


class Main extends Component {
    constructor(props) {
        super(props);
        this.state = {
            postID: window.formBuilderObj.postID,
            restUrl: '/wp-json/register-form/v1/',
            registrationFormData: [],
            response: false,
            defaultField: [
                {
                    'en': {
                        'control': 'input',
                        'type': 'text',
                        'required': true,
                        'label': 'First Name',
                        'id': 'enFirstName',
                        'className': 'form-control',
                        'name': 'enFirstName',
                        'language': 'English',
                        'abbrivation': 'en'
                    },
                    'ar': {
                        'control': 'input',
                        'type': 'text',
                        'required': true,
                        'label': 'الاسم الاول',
                        'id': 'arFirstName',
                        'className': 'form-control',
                        'name': 'arFirstName',
                        'language': 'Arabic',
                        'abbrivation': 'ar'
                    }

                }, {
                    'en': {
                        'control': 'input',
                        'type': 'text',
                        'required': true,
                        'label': 'Last Name',
                        'id': 'enLastName',
                        'className': 'form-control',
                        'name': 'enLastName',
                        'language': 'English',
                        'abbrivation': 'en'
                    },
                    'ar': {
                        'control': 'input',
                        'type': 'text',
                        'required': true,
                        'label': 'الكنية',
                        'id': 'arLastName',
                        'className': 'form-control',
                        'name': 'arLastName',
                        'language': 'Arabic',
                        'abbrivation': 'ar'
                    }
                }, {
                    'en': {
                        'control': 'input',
                        'type': 'email',
                        'required': true,
                        'label': 'Email',
                        'id': 'enEmail',
                        'className': 'form-control',
                        'name': 'enEmail',
                        'language': 'English',
                        'abbrivation': 'en'
                    },
                    'ar': {
                        'control': 'input',
                        'type': 'email',
                        'required': true,
                        'label': 'البريد الإلكتروني',
                        'id': 'arEmail',
                        'className': 'form-control',
                        'name': 'arEmail',
                        'language': 'Arabic',
                        'abbrivation': 'ar'
                    }
                }
            ]
        };
    }

    componentDidMount() {
        if (null !== document.getElementById('registration_form')) {
            let body = document.body;
            body.classList.add('is-loading');
            const requestOptions = {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    postID: this.state.postID,
                }),
            };
            fetch(`${this.state.restUrl}get-form`, requestOptions)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let registrationFormData = [];
                        0 < data.registration_form_data.length ? data.registration_form_data.map((item, index) => {
                            registrationFormData.push(item);
                        }) : registrationFormData.push({
                            'en': {
                                'control': 'input',
                                'type': 'text',
                                'required': true,
                                'label': 'First Name',
                                'id': 'enFirstName',
                                'className': 'form-control',
                                'name': 'enFirstName',
                                'language': 'English',
                                'abbrivation': 'en'
                            },
                            'ar': {
                                'control': 'input',
                                'type': 'text',
                                'required': true,
                                'label': 'الاسم الاول',
                                'id': 'arFirstName',
                                'className': 'form-control',
                                'name': 'arFirstName',
                                'language': 'Arabic',
                                'abbrivation': 'ar'
                            }

                        }, {
                            'en': {
                                'control': 'input',
                                'type': 'text',
                                'required': true,
                                'label': 'Last Name',
                                'id': 'enLastName',
                                'className': 'form-control',
                                'name': 'enLastName',
                                'language': 'English',
                                'abbrivation': 'en'
                            },
                            'ar': {
                                'control': 'input',
                                'type': 'text',
                                'required': true,
                                'label': 'الكنية',
                                'id': 'arLastName',
                                'className': 'form-control',
                                'name': 'arLastName',
                                'language': 'Arabic',
                                'abbrivation': 'ar'
                            }
                        }, {
                            'en': {
                                'control': 'input',
                                'type': 'email',
                                'required': true,
                                'label': 'Email',
                                'id': 'enEmail',
                                'className': 'form-control',
                                'name': 'enEmail',
                                'language': 'English',
                                'abbrivation': 'en'
                            },
                            'ar': {
                                'control': 'input',
                                'type': 'email',
                                'required': true,
                                'label': 'البريد الإلكتروني',
                                'id': 'arEmail',
                                'className': 'form-control',
                                'name': 'arEmail',
                                'language': 'Arabic',
                                'abbrivation': 'ar'
                            }
                        });
                        body.classList.remove('is-loading');
                        this.setState({registrationFormData: registrationFormData, response: true});

                    }
                });
        }
    }

    handleSave = (event) => {
        event.preventDefault();
        if( '' === $('.post-type-registration-forms #titlewrap input').val() ){
            alert('Please enter a title to publish this registration form.');
        }else{
            let body = document.body;
            body.classList.add('is-loading');
            const requestOptions = {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    postID: this.state.postID,
                    registrationFormData: this.state.registrationFormData,
                }),
            };
            fetch(`${this.state.restUrl}add-form-data`, requestOptions)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        $('#publish').trigger('click');
                    }
                });
        }


    };
    allFieldsData = (data) => {
        this.setState({registrationFormData: data});
    };

    render() {
        const {response, registrationFormData} = this.state;
        return (
            <div id="registration-template" className="registration-template">
                <div className="button-wrap">
                    <button className="button button-primary btn-save" onClick={this.handleSave}>Save</button>
                </div>
                <div className="compulsory-field-main">
                    {response &&
                    <CompulsoryFields wpObject={window.formBuilderObj} registrationFormData={registrationFormData}/>}
                </div>
                <div className="additional-field-main">
                    {response &&
                    <AdditionalFields wpObject={window.formBuilderObj} registrationFormData={registrationFormData}
                                      allFieldsData={this.allFieldsData}/>}
                </div>
            </div>
        );
    }
}

export default Main;

document.addEventListener('DOMContentLoaded', function () {
    if (null !== document.getElementById('registration-form-wrap')) {
        ReactDOM.render(<Main/>, document.getElementById('registration-form-wrap'));
    }
});


