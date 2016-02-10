<?

namespace CodeCraft\Helpers;

/**
 * Form helper class. Unless otherwise noted, all generated HTML will be made
 * safe. This prevents against simple XSS attacks that could otherwise be triggered
 * by inserting HTML characters into form fields.
 *
 */
class Form {

    /**
     * Generates an opening HTML form tag.
     *
     *     // Form will submit back to the current page using POST
     *     echo self::open();
     *
     *     // Form will submit to 'search' using GET
     *     echo self::open('search', array('method' => 'get'));
     *
     *     // When "file" inputs are present, you must include the "enctype"
     *     echo self::open(NULL, array('enctype' => 'multipart/form-data'));
     *
     * @param   mixed $action     form action, defaults to the current request URI, or [Request] class to use
     * @param   array $attributes html attributes
     *
     * @return  string
     * @uses    self::attributes
     */
    public static function open($action = null, array $attributes = null) {
        if (!$action) {
            // Use the current URI
            $action = POST_FORM_ACTION_URI;
        }

        // Add the form action to the attributes
        $attributes['action'] = $action;

        // Only accept the default character set
        $attributes['accept-charset'] = SITE_CHARSET;

        if (!isset($attributes['method'])) {
            // Use POST method
            $attributes['method'] = 'post';
        }

        return '<form' . self::attributes($attributes) . '>';
    }

    /**
     * Creates the closing form tag.
     *
     *     echo self::close();
     *
     * @return  string
     */
    public static function close() {
        return '</form>';
    }

    /**
     * Creates a form input. If no type is specified, a "text" type input will
     * be returned.
     *
     *     echo self::input('username', $username);
     *
     * @param   string $name       input name
     * @param   string $value      input value
     * @param   array  $attributes html attributes
     *
     * @return  string
     */
    public static function input($name, $value = null, array $attributes = null) {
        // Set the input name
        $attributes['name'] = $name;

        // Set the input value
        $attributes['value'] = $value;

        if (!isset($attributes['type'])) {
            // Default type is text
            $attributes['type'] = 'text';
        }

        return '<input' . self::attributes($attributes) . ' />';
    }

    /**
     * Creates a hidden form input.
     *
     *     echo self::hidden('csrf', $token);
     *
     * @param   string $name       input name
     * @param   string $value      input value
     * @param   array  $attributes html attributes
     *
     * @return  string
     * @uses    self::input
     */
    public static function hidden($name, $value = null, array $attributes = null) {
        $attributes['type'] = 'hidden';

        return self::input($name, $value, $attributes);
    }

    /**
     * Creates a password form input.
     *
     *     echo self::password('password');
     *
     * @param   string $name       input name
     * @param   string $value      input value
     * @param   array  $attributes html attributes
     *
     * @return  string
     * @uses    self::input
     */
    public static function password($name, $value = null, array $attributes = null) {
        $attributes['type'] = 'password';

        return self::input($name, $value, $attributes);
    }

    /**
     * Creates a file upload form input. No input value can be specified.
     *
     *     echo self::file('image');
     *
     * @param   string $name       input name
     * @param   array  $attributes html attributes
     *
     * @return  string
     * @uses    self::input
     */
    public static function file($name, array $attributes = null) {
        $attributes['type'] = 'file';

        return self::input($name, null, $attributes);
    }

    /**
     * Creates a checkbox form input.
     *
     *     echo self::checkbox('remember_me', 1, (bool) $remember);
     *
     * @param   string  $name       input name
     * @param   string  $value      input value
     * @param   boolean $checked    checked status
     * @param   array   $attributes html attributes
     *
     * @return  string
     * @uses    self::input
     */
    public static function checkbox($name, $value = null, $checked = false, array $attributes = null) {
        $attributes['type'] = 'checkbox';

        if ($checked === true) {
            // Make the checkbox active
            $attributes[] = 'checked';
        }

        return self::input($name, $value, $attributes);
    }

    /**
     * Creates a radio form input.
     *
     *     echo self::radio('like_cats', 1, $cats);
     *     echo self::radio('like_cats', 0, ! $cats);
     *
     * @param   string  $name       input name
     * @param   string  $value      input value
     * @param   boolean $checked    checked status
     * @param   array   $attributes html attributes
     *
     * @return  string
     * @uses    self::input
     */
    public static function radio($name, $value = null, $checked = false, array $attributes = null) {
        $attributes['type'] = 'radio';

        if ($checked === true) {
            // Make the radio active
            $attributes[] = 'checked';
        }

        return self::input($name, $value, $attributes);
    }

    /**
     * Creates a textarea form input.
     *
     *     echo self::textarea('about', $about);
     *
     * @param   string $name       textarea name
     * @param   string $body       textarea body
     * @param   array  $attributes html attributes
     *
     * @return  string
     */
    public static function textarea($name, $body = '', array $attributes = null) {
        // Set the input name
        $attributes['name'] = $name;

        // Add default rows and cols attributes (required)
        $attributes += array('rows' => 10, 'cols' => 50);

        return '<textarea' . self::attributes($attributes) . '>' . htmlspecialchars($body, ENT_QUOTES) . '</textarea>';
    }

    /**
     * Creates a select form input.
     *
     *     echo self::select('country', $countries, $country);
     *
     * [!!] Support for multiple selected options was added in v3.0.7.
     *
     * @param   string $name       input name
     * @param   array  $options    available options
     * @param   mixed  $selected   selected option string, or an array of selected options
     * @param   array  $attributes html attributes
     *
     * @return  string
     */
    public static function select($name, array $options = null, $selected = null, array $attributes = null) {
        // Set the input name
        $attributes['name'] = $name;

        if (is_array($selected)) {
            // This is a multi-select, god save us!
            $attributes[] = 'multiple';
        }

        if (!is_array($selected)) {
            if ($selected === null) {
                // Use an empty array
                $selected = array();
            } else {
                // Convert the selected options to an array
                $selected = array((string)$selected);
            }
        }

        if (empty($options)) {
            // There are no options
            $options = '';
        } else {
            foreach ($options as $value => $name) {
                if (is_array($name)) {
                    // Create a new optgroup
                    $group = array('label' => $value);

                    // Create a new list of options
                    $_options = array();

                    foreach ($name as $_value => $_name) {
                        // Force value to be string
                        $_value = (string)$_value;

                        // Create a new attribute set for this option
                        $option = array('value' => $_value);

                        if (in_array($_value, $selected)) {
                            // This option is selected
                            $option[] = 'selected';
                        }

                        // Change the option to the HTML string
                        $_options[] = '<option' . self::attributes($option) . '>' . htmlspecialchars($_name) . '</option>';
                    }

                    // Compile the options into a string
                    $_options = "\n" . implode("\n", $_options) . "\n";

                    $options[$value] = '<optgroup' . self::attributes($group) . '>' . $_options . '</optgroup>';
                } else {
                    // Force value to be string
                    $value = (string)$value;

                    // Create a new attribute set for this option
                    $option = array('value' => $value);

                    if (in_array($value, $selected)) {
                        // This option is selected
                        $option[] = 'selected';
                    }

                    // Change the option to the HTML string
                    $options[$value] = '<option' . self::attributes($option) . '>' . htmlspecialchars($name) . '</option>';
                }
            }

            // Compile the options into a single string
            $options = "\n" . implode("\n", $options) . "\n";
        }

        return '<select' . self::attributes($attributes) . '>' . $options . '</select>';
    }

    /**
     * Creates a submit form input.
     *
     *     echo self::submit(NULL, 'Login');
     *
     * @param   string $name       input name
     * @param   string $value      input value
     * @param   array  $attributes html attributes
     *
     * @return  string
     * @uses    self::input
     */
    public static function submit($name, $value, array $attributes = null) {
        $attributes['type'] = 'submit';

        return self::input($name, $value, $attributes);
    }

    /**
     * Creates a image form input.
     *
     *     echo self::image(NULL, NULL, array('src' => 'media/img/login.png'));
     *
     * @param   string  $name       input name
     * @param   string  $value      input value
     * @param   array   $attributes html attributes
     * @param   boolean $index      add index file to URL?
     *
     * @return  string
     * @uses    self::input
     */
    public static function image($name, $value, array $attributes = null, $index = false) {
        if (!empty($attributes['src'])) {
            if (strpos($attributes['src'], '://') === false) {
                // Add the base URL
                $attributes['src'] = URL::base($index) . $attributes['src'];
            }
        }

        $attributes['type'] = 'image';

        return self::input($name, $value, $attributes);
    }

    /**
     * Creates a button form input. Note that the body of a button is NOT escaped,
     * to allow images and other HTML to be used.
     *
     *     echo self::button('save', 'Save Profile', array('type' => 'submit'));
     *
     * @param   string $name       input name
     * @param   string $body       input value
     * @param   array  $attributes html attributes
     *
     * @return  string
     */
    public static function button($name, $body, array $attributes = null) {
        // Set the input name
        $attributes['name'] = $name;

        return '<button' . self::attributes($attributes) . '>' . $body . '</button>';
    }

    /**
     * Creates a form label. Label text is not automatically translated.
     *
     *     echo self::label('username', 'Username');
     *
     * @param   string $input      target input
     * @param   string $text       label text
     * @param   array  $attributes html attributes
     *
     * @return  string
     */
    public static function label($input, $text = null, array $attributes = null) {
        if ($text === null) {
            // Use the input name as the text
            $text = ucwords(preg_replace('/[\W_]+/', ' ', $input));
        }

        // Set the label target
        $attributes['for'] = $input;

        return '<label' . self::attributes($attributes) . '>' . $text . '</label>';
    }

    /**
     * @var  array  preferred order of attributes
     */
    protected static $attribute_order = array(
        'action',
        'method',
        'type',
        'id',
        'name',
        'value',
        'href',
        'src',
        'width',
        'height',
        'cols',
        'rows',
        'size',
        'maxlength',
        'rel',
        'media',
        'accept-charset',
        'accept',
        'tabindex',
        'accesskey',
        'alt',
        'title',
        'class',
        'style',
        'selected',
        'checked',
        'readonly',
        'disabled',
    );

    /**
     * Compiles an array of HTML attributes into an attribute string.
     * Attributes will be sorted using self::$attribute_order for consistency.
     *
     * @param   array $attributes attribute list
     *
     * @return  string
     */
    protected static function attributes(array $attributes = null) {
        if (empty($attributes))
            return '';

        $sorted = array();
        foreach (self::$attribute_order as $key) {
            if (isset($attributes[$key])) {
                // Add the attribute to the sorted list
                $sorted[$key] = $attributes[$key];
            }
        }

        // Combine the sorted attributes
        $attributes = $sorted + $attributes;

        $compiled = '';
        foreach ($attributes as $key => $value) {
            if ($value === null) {
                // Skip attributes that have NULL values
                continue;
            }

            if (is_int($key)) {
                // Assume non-associative keys are mirrored attributes
                $key = $value;
            }

            // Add the attribute key
            $compiled .= ' ' . $key;

            if ($value) {
                // Add the attribute value
                $compiled .= '="' . htmlspecialchars($value) . '"';
            }
        }

        return $compiled;
    }

}
