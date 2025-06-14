# Example: bug_report.md

name: 🐛 Bug Report
description: Báo cáo lỗi để nhóm dev kiểm tra và sửa lỗi
title: "[BUG] "
labels: [bug]
assignees: ""

body:
  - type: markdown
    attributes:
      value: "Cảm ơn bạn đã báo lỗi! Hãy điền thông tin bên dưới để chúng tôi hỗ trợ tốt hơn."

  - type: input
    id: steps
    attributes:
      label: "Các bước để tái hiện lỗi"
      description: "Ghi rõ từng bước dẫn tới lỗi"
      placeholder: "1. Mở app\n2. Bấm nút XYZ..."
    validations:
      required: true

  - type: textarea
    id: expected
    attributes:
      label: "Kết quả mong đợi"
      description: "Bạn mong đợi điều gì sẽ xảy ra?"
    validations:
      required: false

  - type: textarea
    id: actual
    attributes:
      label: "Kết quả thực tế"
      description: "Điều gì đã xảy ra thực tế?"
    validations:
      required: false

  - type: input
    id: env
    attributes:
      label: "Môi trường"
      placeholder: "Ví dụ: iOS 17, Chrome 122.0.0, Ubuntu 22.04..."
