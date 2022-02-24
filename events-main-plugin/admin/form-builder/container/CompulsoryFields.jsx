import React, {Component} from 'react';
import PropTypes from 'prop-types';

class CompulsoryFields extends Component {
    constructor(props) {
        super(props);
        this.state = {
            templateName: '',
            enFirstName: '',
            arFirstName: '',
            enLastName: '',
            arLastName: '',
            enEmail: '',
            arEmail: '',
            restUrl: '/wp-json/register-form/v1/',
            postID: this.props.wpObject.postID,
            registrationFormData: this.props.registrationFormData,
            compulsoryFieldsObj: []

        };
    }

    componentDidMount() {
        if (null !== document.getElementById('compulsory-fields-wrap')) {
            let body = document.body;
            body.classList.add('registration-form-body');
            let compulsoryFieldsObj = [];
            0 < this.state.registrationFormData.length &&
            this.state.registrationFormData.map((item, index) => {
                if (3 > index) {
                    compulsoryFieldsObj.push(item);
                }
            });
            this.setState({compulsoryFieldsObj});
        }
    }

    handleChange = (event) => {
        this.setState({
            [event.target.name]: event.target.value
        });
    };

    render() {
        const {compulsoryFieldsObj} = this.state;
        return (
            <div id="compulsory-fields-wrap" className="compulsory-fields-wrap">
                <h3>Compulsory Fields</h3>
                {
                    0 < compulsoryFieldsObj.length && compulsoryFieldsObj.map((item, index) => {
                        const enArr = item.en;
                        const arArr = item.ar;
                        return (
                            <div className="field-wrap" key={index}>
                                <div className="field-inner">
                                    <div className="field-container en-field">
                                        <span className="field-label">{enArr.label}</span>
                                        <label htmlFor={enArr.id} className="screen-reader-text">{enArr.label}</label>
                                            <input
                                                type={enArr.type}
                                                name={enArr.id}
                                                id={enArr.id}
                                                value={this.state[enArr.id]}
                                                className="form-input"
                                                onChange={this.handleChange}
                                            />

                                    </div>
                                    <div className="field-container ar-field">
                                        <span className="field-label">{arArr.label}</span>
                                        <label htmlFor={arArr.id} className="screen-reader-text">{arArr.label}</label>
                                            <input
                                                type={arArr.type}
                                                name={arArr.id}
                                                id={arArr.id}
                                                value={this.state[arArr.id]}
                                                className="form-input"
                                                onChange={this.handleChange}
                                            />
                                    </div>
                                </div>
                            </div>
                        );
                    })
                }
            </div>
        );
    }
}

export default CompulsoryFields;
CompulsoryFields.propTypes = {
    registrationFormData: PropTypes.func.isRequired,
    wpObject: PropTypes.func.isRequired,
};