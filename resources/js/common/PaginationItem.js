export default function PaginationItem(props) {
  const { active, disabled, page, text, setPage } = props

  return (
    <li
      className={
        'page-item ' + (active ? 'active ' : '') + (disabled ? 'disabled ' : '')
      }
      style={{ cursor: disabled ? 'not-allowed ' : 'pointer' }}
    >
      <a className="page-link" onClick={() => setPage(page)}>
        {text}
      </a>
    </li>
  )
}
