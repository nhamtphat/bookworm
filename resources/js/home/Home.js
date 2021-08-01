import React, { useEffect, useState } from 'react'
import Layout from '../layouts/Layout'
import BookGridFigure from '../common/BookGridFigure'
import Slider from 'react-slick'
import 'slick-carousel/slick/slick.css'
import 'slick-carousel/slick/slick-theme.css'
import axios from 'axios'
import { Helmet } from 'react-helmet'
import '../../css/Home.css'
import { Link } from 'react-router-dom'
import LoadingSpin from '../common/LoadingSpin'

export default function Home() {
  const [onSaleBook, setOnSaleBooks] = useState([])
  const [recommendedBooks, setRecommendedBooks] = useState([])
  const [popularBooks, setPopularBooks] = useState([])
  const [view, setView] = useState('recommended')

  useEffect(() => {
    axios
      .get('/api/books', {
        params: {
          limit: 10,
          sort: '-sub_price,final_price',
        },
      })
      .then((response) => {
        setOnSaleBooks(response.data.data)
      })

    axios
      .get('/api/books', {
        params: {
          limit: 8,
          sort: '-avg_star,final_price',
        },
      })
      .then((response) => {
        setRecommendedBooks(response.data.data)
      })

    axios
      .get('/api/books', {
        params: {
          limit: 8,
          sort: '-reviews_count,final_price',
        },
      })
      .then((response) => {
        setPopularBooks(response.data.data)
      })
  }, [])

  const settings = {
    infinite: true,
    swipeToSlide: true,
    slidesToShow: 4,
    dots: true,
    responsive: [
      {
        breakpoint: 640,
        settings: {
          slidesToShow: 1,
        },
      },
    ],
  }

  function dataIsReady() {
    return !(recommendedBooks.length == 0 || popularBooks.length == 0)
  }

  return (
    <Layout>
      <Helmet>
        <title>Bookworm Homepage</title>
      </Helmet>

      <section className="section-content bg padding-y">
        {!dataIsReady() ? <LoadingSpin /> : null}

        <div className={'container ' + (dataIsReady() ? '' : 'd-none')}>
          {onSaleBook.length > 0 ? (
            <div>
              <div className="row">
                <div className="col-12">
                  <h4 className="d-inline">On Sale</h4>
                  <Link to="/shop" className="btn btn-secondary float-right">
                    View all <i className="fas fa-caret-right ml-2"></i>
                  </Link>
                </div>
              </div>

              <Slider {...settings}>
                {onSaleBook.map((book) => (
                  <div key={book.id} className="item-slide p-2">
                    <BookGridFigure book={book} />
                  </div>
                ))}
              </Slider>
            </div>
          ) : null}

          <div className="p-3 mt-5 text-center">
            <h4>Featured Books</h4>

            <div className="btn-group text-center">
              <button
                className={
                  'btn btn-outline-secondary ' +
                  (view === 'recommended' ? 'active' : '')
                }
                onClick={() => setView('recommended')}
                title="List view"
              >
                Recommended
              </button>
              <button
                className={
                  'btn btn-outline-secondary ' +
                  (view === 'popular' ? 'active' : '')
                }
                onClick={() => setView('popular')}
                title="Grid view"
              >
                Popular
              </button>
            </div>
          </div>

          <div className="row">
            {recommendedBooks.length == 0 || popularBooks.length == 0 ? (
              <LoadingSpin />
            ) : null}

            {(view == 'recommended' ? recommendedBooks : popularBooks).map(
              (book) => (
                <div key={book.id} className="col-md-3">
                  <div className="item-slide p-2">
                    <BookGridFigure book={book} />
                  </div>
                </div>
              ),
            )}
          </div>
        </div>
      </section>
    </Layout>
  )
}
