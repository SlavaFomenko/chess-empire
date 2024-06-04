import React, { useState } from "react";
import styles from "../styles/edit-profile-dialog.module.scss";
import { BannerLayout } from "../../../../layouts/banner-layout";
import { Field, Form, Formik } from "formik";

export function EditProfileDialog ({ state, setState, onSubmit }) {
  const [showPassword, setShowPassword] = useState({ old: false, new: false });
  const initialValues = { ...state.data, oldPassword: "", newPassword: "", password_confirm: "" };

  const validateForm = (values) => {
    const errors = {};
    const requiredFields = ["email", "username", "firstName", "lastName", "newPassword"];
    requiredFields.forEach(field => {
      const error = validate[field](values[field]);
      if (error) {
        errors[field] = validate[field](values[field]);
      }
    });
    if (values.newPassword !== values.password_confirm && !errors.password_confirm) {
      errors.password_confirm = "The password confirmation does not match";
    }
    return errors;
  };

  const validate = {
    email: (value) => {
      let error;
      if (!/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/.test(value)) {
        error = "Invalid email";
      }
      return error;
    },
    username: (value) => {
      let error;
      if (!/^[a-zA-Z0-9]+$/.test(value)) {
        error = "Username can only contain letters and digits";
      }
      return error;
    },
    firstName: (value) => {
      let error;
      if (!/^[a-zA-Z]+$/.test(value)) {
        error = "First name can only contain letters";
      }
      return error;
    },
    lastName: (value) => {
      let error;
      if (!/^[a-zA-Z]+$/.test(value)) {
        error = "Last name can only contain letters";
      }
      return error;
    },
    newPassword: (value) => {
      let error;
      if (!value || value?.trim() === "") {
        return null;
      }
      if (value?.length < 8) {
        error = "The password must be at least 8 characters long";
      } else if (!/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/.test(value)) {
        error = "The password must contain at least 1 digit and 1 lowercase and a capital letter";
      }
      return error;
    }
  };

  const onSubmitRegistration = (values) => {
    const keys = ["email", "username", "firstName", "lastName", "oldPassword", "newPassword"];
    const data = {};
    keys.forEach(key => {
      if (values[key] !== initialValues[key]) data[key] = values[key];
    });
    onSubmit(data);
  };

  const toggleShowPassword = (key) => {
    setShowPassword({ ...showPassword, [key]: !showPassword[key] });
  };

  return (
    <BannerLayout onClick={() => setState({ ...state, selectedFile: null, opened: false })}>
      <div className={styles.editProfileDialog} onClick={e => e.stopPropagation()}>
        <Formik initialValues={initialValues} validate={validateForm} onSubmit={onSubmitRegistration}>
          {({ errors, touched, values, handleChange, handleBlur, handleSubmit }) => (
            <Form onSubmit={handleSubmit}>
              <h2>Profile info:</h2>
              <Field
                type="email"
                name="email"
                placeholder="Email"
                onChange={handleChange}
                onBlur={handleBlur}
                value={values.email}
                validate={validate.email}
              />
              {errors.email && touched.email && <p className={styles.error}>{errors.email}</p>}

              <Field
                type="text"
                name="username"
                placeholder="Username"
                onChange={handleChange}
                onBlur={handleBlur}
                value={values.username}
                validate={validate.username}
              />
              {errors.username && touched.username && <p className={styles.error}>{errors.username}</p>}

              <Field
                type="text"
                name="firstName"
                placeholder="First name"
                onChange={handleChange}
                onBlur={handleBlur}
                value={values.firstName}
                validate={validate.firstName}
              />
              {errors.firstName && touched.firstName && <p className={styles.error}>{errors.firstName}</p>}

              <Field
                type="text"
                name="lastName"
                placeholder="Last name"
                onChange={handleChange}
                onBlur={handleBlur}
                value={values.lastName}
                validate={validate.lastName}
              />
              {errors.lastName && touched.lastName && <p className={styles.error}>{errors.lastName}</p>}

              <h2>Password:</h2>
              <div>
                <Field
                  type={showPassword?.old ? "text" : "password"}
                  name="oldPassword"
                  placeholder="Old password"
                  onChange={handleChange}
                  onBlur={handleBlur}
                  value={values.password}
                  validate={validate.password}
                />
                <button className={styles.showPasswordToggle} type="button" onClick={() => toggleShowPassword("old")}>
                  {showPassword?.old ? "◡" : "⨀"}
                </button>
              </div>

              <div>
                <Field
                  type={showPassword?.new ? "text" : "password"}
                  name="newPassword"
                  placeholder="New password"
                  onChange={handleChange}
                  onBlur={handleBlur}
                  value={values.password}
                  validate={validate.password}
                />
                <button className={styles.showPasswordToggle} type="button" onClick={() => toggleShowPassword("new")}>
                  {showPassword?.new ? "◡" : "⨀"}
                </button>
              </div>
              {errors.password && touched.password && <p className={styles.error}>{errors.password}</p>}

              <Field
                type="password"
                name="password_confirm"
                placeholder="Confirm password"
                onChange={handleChange}
                onBlur={handleBlur}
                value={values.password_confirm}
              />
              {errors.password_confirm && touched.password_confirm &&
                <p className={styles.error}>{errors.password_confirm}</p>}

              <button type="submit">Submit</button>
            </Form>
          )}
        </Formik>
      </div>
    </BannerLayout>
  );
}
