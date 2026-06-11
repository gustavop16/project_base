const INPUT_TYPE_LABELS = {
  text:            'Texto livre',
  select_simple:   'Select simples',
  select_multiple: 'Select múltiplo',
  radio:           'Radio button',
  checkbox:        'Checkbox',
  upload:          'Upload de arquivo',
};

export default class QuestionModel {
  constructor({
    id,
    question,
    description,
    score,
    input_type,
    options,
  }) {
    this.id               = id;
    this.question         = question;
    this.description      = description;
    this.score            = score;
    this.input_type       = input_type;
    this.input_type_label = INPUT_TYPE_LABELS[input_type] ?? input_type;
    this.options          = options ?? [];
  }
}
