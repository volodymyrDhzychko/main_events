import React, {Component, Fragment} from 'react';
import AddNewFieldModal from './AddNewFieldModal.jsx';
import EditFieldModal from './editFieldModal.jsx';
import {confirmAlert} from 'react-confirm-alert';
import PropTypes from 'prop-types';

class AdditionalFields extends Component {
    constructor(props) {
        super(props);
        this.state = {
            modalPopup: false,
            registrationFormData: this.props.registrationFormData,
            additionalFieldsObj: [],
            compulsoryFieldsObj: [],
            editFieldData: [],
            editModalField: false,
            editIndex: 0,
        };

    }

    componentDidMount() {
        if (null !== document.getElementById('additional-fields-wrap')) {
            let additionalFieldsObj = [];
            let compulsoryFieldsObj = [];
            let i = 0;

            (3 < this.state.registrationFormData.length) &&
            this.state.registrationFormData.map((item, index) => {
                if (2 < index) {
                    if (null !== item) {
                        additionalFieldsObj.push(item);
                    }
                    return false;
                }
                compulsoryFieldsObj.push(item);
            });

            this.setState({additionalFieldsObj, compulsoryFieldsObj});
        }
    }

    handleModal = (event) => {
        event.preventDefault();
        this.setState({modalPopup: true});
    };
    handleModelClose = event => {
        this.setState({modalPopup: false});
    };
    handleAddField = (newFieldObj) => {
        let additionalFieldsObj  = this.state.additionalFieldsObj;
        let registrationFormData = this.state.registrationFormData;

        additionalFieldsObj.push(newFieldObj);
        registrationFormData.push(newFieldObj);

        this.props.allFieldsData(registrationFormData);

        this.setState({
            additionalFieldsObj: additionalFieldsObj,
            registrationFormData: registrationFormData,
            modalPopup: false
        });

    };
    handleEditedFieldData = (newFieldObj) => {
        let additionalFieldsObj  = this.state.additionalFieldsObj;
        let registrationFormData = this.state.registrationFormData;
        const editIndex          = this.state.editIndex;

        additionalFieldsObj[editIndex] = newFieldObj[0];
        registrationFormData[parseInt(editIndex) + 3] = newFieldObj[0];

        this.props.allFieldsData(registrationFormData);

        this.setState({
            additionalFieldsObj: additionalFieldsObj,
            registrationFormData: registrationFormData,
            editModalField: false
        });

    };
    handleDeleteField = (event) => {
        const currentIndex = event.currentTarget.attributes.getNamedItem('index').value;

        confirmAlert({
            customUI: ({onClose}) => {
                return (
                    <div className='custom-ui'>
                        <h1>Confirmation</h1>
                        <p className="del-msg">Are you sure you wish to delete this field?</p>
                        <div className="react-button-group">
                            <button onClick={onClose} className="btn-cancel">No</button>
                            <button
                                onClick={(e) => {
                                    let registrationFormData = this.state.registrationFormData;
                                    registrationFormData = registrationFormData.filter((cItem, index) => parseInt(index) !== parseInt(currentIndex) + 3);
                                    this.props.allFieldsData(registrationFormData);
                                    this.setState({additionalFieldsObj: this.state.additionalFieldsObj.filter((cItem, index) => parseInt(index) !== parseInt(currentIndex))});
                                    this.setState({registrationFormData: registrationFormData});
                                    onClose();
                                }}
                                className="btn-yes"
                            >
                                Yes, Delete it!
                                <span className="dashicons dashicons-update spinner-icon" />
                            </button>
                        </div>
                    </div>
                );
            }
        });
    };
    handleMoveUp = (event) => {
        const {additionalFieldsObj, compulsoryFieldsObj} = this.state;
        let additionalFieldsSwapObj = additionalFieldsObj;
        const currentIndex   = event.currentTarget.attributes.getNamedItem('index').value;
        const UpElementIndex = parseInt(currentIndex) - 1;

        var b = additionalFieldsSwapObj[currentIndex];
        additionalFieldsSwapObj[currentIndex] = additionalFieldsSwapObj[UpElementIndex];
        additionalFieldsSwapObj[UpElementIndex] = b;

        const registrationFormSwapObj = [...compulsoryFieldsObj, ...additionalFieldsSwapObj];
        this.props.allFieldsData(registrationFormSwapObj);
        this.setState({additionalFieldsObj: additionalFieldsSwapObj, registrationFormData: registrationFormSwapObj});

    };
    handleMoveDown = (event) => {
        const {modalPopup, additionalFieldsObj, registrationFormData, compulsoryFieldsObj} = this.state;
        let additionalFieldsSwapObj = additionalFieldsObj;
        const currentIndex     = event.currentTarget.attributes.getNamedItem('index').value;
        const downElementIndex = parseInt(currentIndex) + 1;

        // TODO: Need to replace "b" will normal name and also switch to let or const
        //  Since it was used only once in 2 lines under - we can remove this variable at all
        var b = additionalFieldsSwapObj[currentIndex];
        additionalFieldsSwapObj[currentIndex] = additionalFieldsSwapObj[downElementIndex];
        additionalFieldsSwapObj[downElementIndex] = b;

        const registrationFormSwapObj = [...compulsoryFieldsObj, ...additionalFieldsSwapObj];
        this.props.allFieldsData(registrationFormSwapObj);

        this.setState({additionalFieldsObj: additionalFieldsSwapObj, registrationFormData: registrationFormSwapObj});

    };
    camelCase = (str) => {
        return str.replace(/(?:^\w|[A-Z]|\b\w)/g, function (word, index) {
            return 0 === index ? word.toLowerCase() : word.toUpperCase();
        }).replace(/\s+/g, '');
    };

    handleEditField = (event) => {
        const currentIndex = event.currentTarget.attributes.getNamedItem('index').value;
        let editFieldData  = [];

        editFieldData.push(this.state.additionalFieldsObj[currentIndex]);
        this.setState({editModalField: true, editFieldData: editFieldData, editIndex: currentIndex});
    };
    handleEditModelClose = () => {
        this.setState({editModalField: false});
    };

    // TODO: This render function is very big.
    //  It's difficult to follow the logic, will be better to divide this code to separate function and call in right place.. Benefits of it are: better code structure, easy testing and support
    render() {
        const {modalPopup, additionalFieldsObj, editModalField, editFieldData} = this.state;
        return (
            <div id="additional-fields-wrap" className="additional-fields-wrap">
                <h3> Additional Fields</h3>
                <div className="add-new-fields-wrap" id="add-new-fields-wrap">
                    {
                        0 < additionalFieldsObj.length && additionalFieldsObj.map((item, index) => {
                            const enArr = item.en;
                            const arArr = item.ar;
                            return (
                                <Fragment key={index}>
                                    {'Text Input' === enArr.control &&
                                    <div className="field-wrap">
                                        <div className="field-inner">
                                            <div className="field-container en-field">
                                                <span className="field-label">{enArr.label}{enArr.required &&
                                                <sup className="medatory"> *</sup>}</span>
                                                    <label htmlFor={enArr.id} className="screen-reader-text">{enArr.label}</label>
                                                    <input
                                                        type={enArr.type}
                                                        name={enArr.id}
                                                        id={enArr.id}
                                                        value={this.state[enArr.id]}
                                                        className={enArr.className}
                                                        onChange={this.handleChange}
                                                    />
                                            </div>
                                            <div className="field-container ar-field">
                                                <span className="field-label">{arArr.label}{arArr.required &&
                                                <sup className="medatory"> *</sup>}</span>
                                                    <label htmlFor={arArr.id} className="screen-reader-text">{arArr.label}</label>
                                                    <input
                                                        type={arArr.type}
                                                        name={arArr.id}
                                                        id={arArr.id}
                                                        value={this.state[arArr.id]}
                                                        className={arArr.className}
                                                        onChange={this.handleChange}
                                                    />
                                            </div>
                                        </div>
                                        <div className="field-controles">
                                            <a className="controle-edit" index={index}
                                               onClick={this.handleEditField}><span
                                                className="dashicons dashicons-edit" /></a>
                                            <a className="controle-delete" index={index}
                                               onClick={this.handleDeleteField}><span
                                                className="dashicons dashicons-trash" /></a>
                                            <a className="controle-move-up" index={index}
                                               onClick={this.handleMoveUp}><span
                                                className="dashicons dashicons-arrow-up-alt" /> Move Up</a>
                                            <a className="controle-move-down" index={index}
                                               onClick={this.handleMoveDown}><span
                                                className="dashicons dashicons-arrow-down-alt" /> Move Down</a>
                                        </div>
                                    </div>
                                    }
                                    {'Text Area' === enArr.control &&
                                    <div className="field-wrap">
                                        <div className="field-inner">
                                            <div className="field-container en-field">
                                                <span className="field-label">{enArr.label}{enArr.required &&
                                                <sup className="medatory"> *</sup>}</span>
                                                    <label htmlFor={enArr.id}
                                                           className="screen-reader-text">{enArr.label}</label>
                                                       <textarea
                                                           type="textarea"
                                                           className={enArr.className}
                                                           name={enArr.id}
                                                           id={enArr.id}
                                                           value={this.state[enArr.id]}
                                                           onChange={this.handleChange}
                                                       >
                                                       </textarea>
                                            </div>
                                            <div className="field-container ar-field">
                                                <span className="field-label">{arArr.label}{arArr.required &&
                                                <sup className="medatory"> *</sup>}</span>
                                                    <label htmlFor={arArr.id}
                                                           className="screen-reader-text">{arArr.label}</label>
                                                       <textarea
                                                           type="textarea"
                                                           className={arArr.className}
                                                           name={arArr.id}
                                                           id={arArr.id}
                                                           value={this.state[arArr.id]}
                                                           onChange={this.handleChange}
                                                       >
                                                       </textarea>
                                            </div>
                                        </div>
                                        <div className="field-controles">
                                            <a className="controle-edit" index={index}
                                               onClick={this.handleEditField}><span
                                                className="dashicons dashicons-edit" /></a>
                                            <a className="controle-delete" index={index}
                                               onClick={this.handleDeleteField}><span
                                                className="dashicons dashicons-trash" /></a>
                                            <a className="controle-move-up" index={index}
                                               onClick={this.handleMoveUp}><span
                                                className="dashicons dashicons-arrow-up-alt" /> Move Up</a>
                                            <a className="controle-move-down" index={index}
                                               onClick={this.handleMoveDown}><span
                                                className="dashicons dashicons-arrow-down-alt" /> Move Down</a>
                                        </div>
                                    </div>
                                    }
                                    {'Dropdown Select' === enArr.control &&
                                    <div className="field-wrap">
                                        <div className="field-inner">
                                            <div className="field-container en-field">
                                                <span className="field-label">{enArr.label}{enArr.required && <sup className="medatory"> *</sup>}</span>
                                                <label htmlFor={enArr.id} className="screen-reader-text">{enArr.label}</label>
                                                <select className={enArr.className} name={enArr.id}
                                                        multiple={enArr.multiple} id={enArr.id}>
                                                    <option value="">Choose</option>
                                                    {0 < enArr.values.length && enArr.values.map((optionItem, i) => (
                                                        <option key={i}
                                                                value={optionItem.value}>{optionItem.value}</option>
                                                    ))
                                                    }
                                                </select>
                                            </div>
                                            <div className="field-container ar-field">
                                                <span className="field-label">{arArr.label}{arArr.required &&
                                                <sup className="medatory"> *</sup>}</span>
                                                <label htmlFor={arArr.id} className="screen-reader-text">{arArr.label}</label>
                                                <select className={arArr.className} name={arArr.id}
                                                        multiple={arArr.multiple} id={arArr.id}>
                                                    <option value="">أختر</option>
                                                    {0 < arArr.values.length && arArr.values.map((optionItem, i) => (
                                                        <option key={i}
                                                                value={optionItem.value}>{optionItem.value}</option>
                                                    ))
                                                    }
                                                </select>
                                            </div>
                                        </div>
                                        <div className="field-controles">
                                            <a className="controle-edit" index={index}
                                               onClick={this.handleEditField}><span
                                                className="dashicons dashicons-edit" /></a>
                                            <a className="controle-delete" index={index}
                                               onClick={this.handleDeleteField}><span
                                                className="dashicons dashicons-trash" /></a>
                                            <a className="controle-move-up" index={index}
                                               onClick={this.handleMoveUp}><span
                                                className="dashicons dashicons-arrow-up-alt" /> Move Up</a>
                                            <a className="controle-move-down" index={index}
                                               onClick={this.handleMoveDown}><span
                                                className="dashicons dashicons-arrow-down-alt" /> Move Down</a>
                                        </div>
                                    </div>
                                    }
                                    {'Radio' === enArr.control &&
                                    <div className="field-wrap">
                                        <div className="field-inner">
                                            <div className="field-container en-field">
                                                <span className="field-label">{enArr.label}{enArr.required &&
                                                <sup className="medatory"> *</sup>}</span>
                                                <div className="radio-group">
                                                    {0 < enArr.values.length && enArr.values.map((optionItem, i) => (
                                                        <div className="formbuilder-radio" key={i}>
                                                            <input
                                                                name={enArr.id}
                                                                id={`${enArr.id}${i}`}
                                                                value={optionItem.value}
                                                                type="radio"
                                                            />
                                                            <label
                                                                htmlFor={`${enArr.id}${i}`}>{optionItem.value}</label>
                                                        </div>
                                                    ))
                                                    }
                                                </div>
                                            </div>
                                            <div className="field-container ar-field">
                                                <span className="field-label">{arArr.label}{arArr.required &&
                                                <sup className="medatory"> *</sup>}</span>
                                                <div className="radio-group">
                                                    {0 < arArr.values.length && arArr.values.map((optionItem, i) => (
                                                        <div className="formbuilder-radio" key={i}>
                                                            <input
                                                                name={arArr.id}
                                                                id={`${arArr.id}${i}`}
                                                                value={optionItem.value}
                                                                type="radio"
                                                            />
                                                            <label
                                                                htmlFor={`${arArr.id}${i}`}>{optionItem.value}</label>
                                                        </div>
                                                    ))
                                                    }
                                                </div>
                                            </div>
                                        </div>
                                        <div className="field-controles">
                                            <a className="controle-edit" index={index}
                                               onClick={this.handleEditField}><span
                                                className="dashicons dashicons-edit" /></a>
                                            <a className="controle-delete" index={index}
                                               onClick={this.handleDeleteField}><span
                                                className="dashicons dashicons-trash" /></a>
                                            <a className="controle-move-up" index={index}
                                               onClick={this.handleMoveUp}><span
                                                className="dashicons dashicons-arrow-up-alt" /> Move Up</a>
                                            <a className="controle-move-down" index={index}
                                               onClick={this.handleMoveDown}><span
                                                className="dashicons dashicons-arrow-down-alt" /> Move Down</a>
                                        </div>
                                    </div>
                                    }
                                    {'Checkbox' === enArr.control &&
                                    <div className="field-wrap">
                                        <div className="field-inner">
                                            <div className="field-container en-field">
                                                <span className="field-label">{enArr.label}{enArr.required &&
                                                <sup className="medatory"> *</sup>}</span>
                                                <div className="checkbox-group">
                                                    {0 < enArr.values.length && enArr.values.map((optionItem, i) => (
                                                        <div className="formbuilder-checkbox" key={i}>
                                                            <input
                                                                name={`${enArr.id}[]`}
                                                                id={`${enArr.id}${i}`}
                                                                value={optionItem.value}
                                                                type="checkbox"
                                                            />
                                                            <label
                                                                htmlFor={`${enArr.id}${i}`}>{optionItem.value}</label>
                                                        </div>
                                                    ))
                                                    }
                                                </div>
                                            </div>
                                            <div className="field-container ar-field">
                                                <span className="field-label">{arArr.label}{arArr.required &&
                                                <sup className="medatory"> *</sup>}</span>
                                                <div className="checkbox-group">
                                                    {0 < arArr.values.length && arArr.values.map((optionItem, i) => (
                                                        <div className="formbuilder-checkbox" key={i}>
                                                            <input
                                                                name={`${enArr.id}[]`}
                                                                id={`${arArr.id}${i}`}
                                                                value={optionItem.value}
                                                                type="checkbox"
                                                            />
                                                            <label
                                                                htmlFor={`${arArr.id}${i}`}>{optionItem.value}</label>
                                                        </div>
                                                    ))
                                                    }
                                                </div>
                                            </div>
                                        </div>
                                        <div className="field-controles">
                                            <a className="controle-edit" index={index}
                                               onClick={this.handleEditField}><span
                                                className="dashicons dashicons-edit" /></a>
                                            <a className="controle-delete" index={index}
                                               onClick={this.handleDeleteField}><span
                                                className="dashicons dashicons-trash" /></a>
                                            <a className="controle-move-up" index={index}
                                               onClick={this.handleMoveUp}><span
                                                className="dashicons dashicons-arrow-up-alt" /> Move Up</a>
                                            <a className="controle-move-down" index={index}
                                               onClick={this.handleMoveDown}><span
                                                className="dashicons dashicons-arrow-down-alt" /> Move Down</a>
                                        </div>
                                    </div>
                                    }
                                    {'File Upload' === enArr.control &&
                                    <div className="field-wrap">
                                        <div className="field-inner">
                                            <div className="field-container en-field full-width-field">
                                                <span className="field-label">{enArr.label}{enArr.required &&
                                                <sup className="medatory"> *</sup>}</span>
                                                <div className="field-group">
                                                    <div className="file-upload-wrap">
                                                        <label htmlFor="en_filename"
                                                               className="screen-reader-text">file</label>
                                                            <input
                                                                type="text"
                                                                id="en_filename"
                                                                readOnly={true}
                                                            />
                                                        <label htmlFor={enArr.id}
                                                               className="screen-reader-text">{enArr.id}</label>
                                                        <input
                                                            type="file"
                                                            name={enArr.id}
                                                            id={enArr.id}
                                                            className="form-control"
                                                            style={{display: 'none'}}
                                                            onChange={() => {
                                                                let fileinput = document.getElementById(enArr.id);
                                                                let textinput = document.getElementById('en_filename');
                                                                textinput.value = fileinput.value;
                                                            }}
                                                        />
                                                    </div>
                                                    <div className="button-wrap">
                                                        <label htmlFor={enArr.button1}
                                                               className="screen-reader-text">
                                                            {enArr.button1}
                                                        </label>
                                                        <input
                                                            type="button"
                                                            name={enArr.button1}
                                                            value={enArr.button1}
                                                            id={enArr.button1}
                                                            onClick={() => {
                                                                var fileinput = document.getElementById(enArr.id);
                                                                fileinput.click();
                                                            }}
                                                            className="button button-primary"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="field-container ar-field full-width-field">
                                                <span className="field-label">{arArr.label}{arArr.required &&
                                                <sup className="medatory"> *</sup>}</span>
                                                <div className="field-group">
                                                    <div className="file-upload-wrap">
                                                        <label htmlFor="ar_filename"
                                                               className="screen-reader-text">file</label>
                                                            <input
                                                                type="text"
                                                                id="ar_filename"
                                                                readOnly={true}
                                                            />
                                                            <label htmlFor={arArr.id}
                                                                   className="screen-reader-text">
                                                                {arArr.id}
                                                            </label>
                                                            <input
                                                                type="file"
                                                                name={arArr.id}
                                                                id={arArr.id}
                                                                className="form-control"
                                                                style={{display: 'none'}}
                                                                onChange={() => {
                                                                    let fileinput = document.getElementById(arArr.id);
                                                                    let textinput = document.getElementById('ar_filename');
                                                                    textinput.value = fileinput.value;
                                                                }}
                                                            />
                                                    </div>
                                                    <div className="button-wrap">
                                                            <label htmlFor={arArr.button1}
                                                                   className="screen-reader-text">
                                                                {arArr.button1}
                                                            </label>
                                                            <input
                                                                type="button"
                                                                name={arArr.button1}
                                                                value={arArr.button1}
                                                                id={arArr.button1}
                                                                onClick={() => {
                                                                    var fileinput = document.getElementById(arArr.id);
                                                                    fileinput.click();
                                                                }}
                                                                className="button button-primary"
                                                            />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="field-controles">
                                            <a className="controle-edit" index={index}
                                               onClick={this.handleEditField}><span
                                                className="dashicons dashicons-edit" /></a>
                                            <a className="controle-delete" index={index}
                                               onClick={this.handleDeleteField}><span
                                                className="dashicons dashicons-trash" /></a>
                                            <a className="controle-move-up" index={index}
                                               onClick={this.handleMoveUp}><span
                                                className="dashicons dashicons-arrow-up-alt" /> Move Up</a>
                                            <a className="controle-move-down" index={index}
                                               onClick={this.handleMoveDown}><span
                                                className="dashicons dashicons-arrow-down-alt" /> Move Down</a>
                                        </div>
                                    </div>
                                    }
                                </Fragment>
                            );
                        })
                    }
                    <div className="button-wrap">
                        <button className="button button-primary btn-add-new-fields"
                                onClick={this.handleModal}>Add New Fields</button>
                    </div>
                    {modalPopup &&
                    <div className="add-new-fields-modal">
                        <AddNewFieldModal handleAddField={this.handleAddField}
                                          handleModelClose={this.handleModelClose} />
                    </div>}
                    {editModalField &&
                    <div className="add-new-fields-modal">
                        <EditFieldModal editFieldData={editFieldData}
                                        handleEditField={this.handleEditedFieldData}
                                        handleEditModelClose={this.handleEditModelClose} />
                    </div>}
                </div>
            </div>
        );
    }
}

export default AdditionalFields;
AdditionalFields.propTypes = {
    allFieldsData: PropTypes.func.isRequired,
    registrationFormData: PropTypes.func.isRequired,
};